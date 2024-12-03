<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananResource\Pages;
use App\Filament\Resources\PesananResource\RelationManagers;
use App\Models\Pesanan;
use App\Models\ProdukJadi;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getLabel(): ?string
    {
        return 'Pesanan';
    }   

    public static function getPluralLabel(): string
    {
        return 'Pesanan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // id auto-generated

                // Tanggal Pesan
                DatePicker::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->required()
                    ->default(now()),
                
                // Nama Produk
                Select::make('produk_jadi_id')
                    ->label('Produk')
                    ->relationship('produkJadi', 'nama_produk') // Assuming 'nama' is the column for the product name
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        $jumlah = $get('jumlah') ?? 1; // Get the current value of 'jumlah'
                        $totalHarga = Pesanan::calculateTotalHarga($state, $jumlah);
                        $set('total_harga', $totalHarga); // Set the 'total_harga' state
                    }),

                
                // Jumlah
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        $produkJadiId = $get('produk_jadi_id'); // Get the current 'produk_jadi_id'
                        $totalHarga = Pesanan::calculateTotalHarga($produkJadiId, $state);
                        $set('total_harga', $totalHarga); // Set the 'total_harga' state
                    }),
                
                // Total Harga
                TextInput::make('total_harga')
                    ->label('Total Harga')
                    ->readOnly()
                    ->numeric()
                    ->default(0),
                
                // Nama Pelanggan
                TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->placeholder('Masukkan Nama Pelanggan')
                    ->required(),

                // Alamat Pengiriman
                TextInput::make('alamat_pengiriman')
                    ->label('Alamat Pengiriman')
                    ->placeholder('Masukkan Alamat Pengiriman')
                    ->required(),

                // Kontak Pelanggan
                TextInput::make('kontak_pelanggan')
                    ->label('Kontak Pelanggan')
                    ->placeholder('Masukkan Kontak Pelanggan')
                    ->required(),

                // Status Pesanan
                Select::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->options([
                        'diproses' => 'Diproses',
                        'pending' => 'Tertunda',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                    ])
                    ->required()
                    ->default('pending'),
                
                // Bukti Pembayaran
                FileUpload::make('bukti_pembayaran')
                    ->label('Bukti Pembayaran')
                    ->disk('public') // Ensure you have a disk configured
                    ->directory('bukti_pembayaran'),

                // No Resi
                TextInput::make('no_resi')
                    ->label('No Resi')
                    ->placeholder('Masukkan No Resi'),
                
                // Catatan
                TextInput::make('catatan')
                    ->label('Catatan')
                    ->placeholder('Masukkan Catatan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->sortable(),

                TextColumn::make('tanggal_pesan')
                    ->label('Tanggal Pesan')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('produkJadi.nama_produk')
                    ->label('Nama Produk')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),
                
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')) // menurut PEUBI
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->status_pesanan) {
                        'diproses' => 'Diproses',
                        'pending' => 'Tertunda',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                    })
                    ->color(fn ($record) => match ($record->status_pesanan) {
                        'diproses' => 'primary',
                        'pending' => 'warning',
                        'dikirim' => 'info',
                        'selesai' => 'success',
                    }),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('update_status')
                ->label(fn ($record) => match ($record->status_pesanan) {
                    'pending' => 'Proses Pesanan',
                    'diproses' => 'Kirim Pesanan',
                    'dikirim' => 'Selesaikan Pesanan',
                })
                ->action(function ($record, array $data) {
                    self::handleStatusChange($record, $data);
                })
                ->form(fn ($record) => self::getDynamicForm($record))
                ->color(fn ($record) => match ($record->status_pesanan) {
                    'pending' => 'warning',
                    'diproses' => 'primary',
                    'dikirim' => 'info',
                    'selesai' => 'success',
                })
                ->modalHeading('Update Order Status')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status_pesanan !== 'selesai'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function handleStatusChange($record, array $data)
    {
        if ($record->status_pesanan === 'pending') {
            // Check stok before proceeding (pending status)
            $produk = ProdukJadi::where('id', $record->produk_jadi_id)->first();    
    
            // If the product doesn't exist, show an error
            if (!$produk) {
                Notification::make()
                    ->title('Produk Tidak Ditemukan')
                    ->danger()
                    ->body('Produk tidak ditemukan di database stok.')
                    ->send();
    
                return; // Stop further execution
            }
    
            // If the stock is insufficient, show a warning notification, but allow bukti upload
            if ($produk->stok < $record->jumlah) {
                Notification::make()
                    ->title('Stok Tidak Cukup')
                    ->warning()  // Warning instead of danger
                    ->body('Stok tidak mencukupi. Hanya tersedia ' . $produk->stok . ' unit.')
                    ->send();
            }
    
            // Regardless of stock availability, allow uploading bukti_pembayaran
            $record->update([
                'status_pesanan' => 'diproses',
                'bukti_pembayaran' => $data['bukti_pembayaran'] ?? null,
            ]);
    
            Notification::make()
                ->title('Bukti Pembayaran Diperbarui')
                ->success()
                ->body('Bukti pembayaran berhasil diunggah.')
                ->send();

            return; // Stop further execution
        }
    
        if ($record->status_pesanan === 'diproses') {
            // Check stok before proceeding (diproses status)
            $produk = ProdukJadi::where('id', $record->produk_jadi_id)->first();
    
            // If the product doesn't exist, show an error
            if (!$produk) {
                Notification::make()
                    ->title('Produk Tidak Ditemukan')
                    ->danger()
                    ->body('Produk tidak ditemukan di database stok.')
                    ->send();
    
                return; // Stop further execution
            }
    
            // If the stock is insufficient, show an error and prevent status update
            if ($produk->stok < $record->jumlah) {
                Notification::make()
                    ->title('Stok Tidak Cukup')
                    ->danger()
                    ->body('Stok tidak mencukupi. Hanya tersedia ' . $produk->stok . ' unit.')
                    ->send();
    
                return; // Stop further execution
            }
    
            // Deduct stok if sufficient
            $produk->decrement('stok', $record->jumlah);
    
            // Update status to dikirim
            $record->update([
                'status_pesanan' => 'dikirim',
                'no_resi' => $data['no_resi'] ?? null,
            ]);
    
            Notification::make()
                ->title('Status Pesanan Diperbarui')
                ->success()
                ->body('Status pesanan berhasil diperbarui menjadi Dikirim.')
                ->send();

            return; // Stop further execution
        }
    
        if ($record->status_pesanan === 'dikirim') {
            // For dikirim -> selesai
            $record->update([
                'status_pesanan' => 'selesai',
            ]);
    
            Notification::make()
                ->title('Status Pesanan Diperbarui')
                ->success()
                ->body('Status pesanan berhasil diperbarui menjadi Selesai.')
                ->send();
        }
    }



    private static function getDynamicForm($record)
    {
        // Define dynamic fields based on the order's current status
        return match ($record->status_pesanan) {
            'pending' => [
                FileUpload::make('bukti_pembayaran')
                    ->label('Upload Bukti Pembayaran')
                    ->disk('public')
                    ->directory('bukti_pembayaran')
                    ->required(),
            ],
            'diproses' => [
                TextInput::make('no_resi')
                    ->label('Input Nomor Resi')
                    ->placeholder('Masukkan Nomor Resi')
                    ->required(),
            ],
            default => [], // No form fields required for 'dikirim' or 'selesai'
        };
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}