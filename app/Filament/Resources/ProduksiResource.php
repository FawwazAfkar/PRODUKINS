<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduksiResource\Pages;
use App\Filament\Resources\ProduksiResource\RelationManagers;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProduksiResource extends Resource
{
    protected static ?string $model = Produksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function getLabel(): ?string
    {
        return 'Produksi';
    }
    public static function getPluralLabel(): string
    {
        return 'Produksi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('produk_jadi_id')
                    ->label('Produk Jadi')
                    ->relationship('produkJadi', 'nama_produk')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jumlahProduksi = $get('jumlah_produksi');
                        if ($jumlahProduksi) {
                            $bahanBaku = Produksi::calculateBahanBaku($state, $jumlahProduksi);
                            $set('bahan_baku', $bahanBaku);
                        }
                    }),
    
                TextInput::make('jumlah_produksi')
                    ->label('Jumlah Produksi')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $produkJadiId = $get('produk_jadi_id');
                        if ($produkJadiId) {
                            $set('bahan_baku', Produksi::calculateBahanBaku($produkJadiId, $state));
                        }
                    }),
    
                // Bahan Baku Repeater (list of raw materials)
                Repeater::make('bahan_baku')
                    ->label('Kebutuhan Bahan Baku')
                    ->schema([
                        TextInput::make('nama_bahan')->label('Bahan')->disabled(),
                        TextInput::make('jumlah')->label('Jumlah')->disabled(),
                    ])
                    ->disableItemCreation()
                    ->disableItemDeletion()
                    ->hidden(fn ($state) => empty($state)),
    
                // Tanggal Mulai
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()),
    
                // Jam Mulai
                TimePicker::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->required()
                    ->default(now()->format('H:i')),
    
                // Tanggal Selesai
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->default(null),
    
                // Jam Selesai
                TimePicker::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->default(null),
    
                // Status Produksi
                Select::make('status_produksi')
                    ->label('Status Produksi')
                    ->options([
                        'proses' => 'Dalam Proses',
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }

    protected static function getCreateButtonLabel(): string 
    {
        return 'Tambah';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Produksi')
                    ->sortable(),

                // Displaying 'nama_produk' from related 'produkJadi'
                TextColumn::make('produkJadi.nama_produk')  // Accessing the related field
                    ->label('Nama Produk')
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('jumlah_produksi')
                    ->label('Jumlah Produksi')
                    ->sortable(),

                // Displaying Bahan Baku as a readable string from JSON
                TextColumn::make('bahan_baku')
                    ->label('Bahan Baku')
                    ->wrap()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        // Checking if 'bahan_baku' contains data and returning it as a string
                        if ($record->bahan_baku && is_array($record->bahan_baku)) {
                            return collect($record->bahan_baku)
                                ->map(fn($item) => $item['nama_bahan'] . ' - ' . $item['jumlah'])
                                ->implode(', ');  // Combines the array into a readable string
                        }
                        return 'No Bahan Baku';  // In case it's empty or null
                    }),

                TextColumn::make('status_produksi')
                    ->badge()
                    ->label('Status Produksi')
                    ->getStateUsing(fn ($record) => match ($record->status_produksi) {
                        'proses' => 'Dalam Proses',
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                    })
                    ->color(fn ($record) => match ($record->status_produksi) {
                        'proses' => 'warning',
                        'pending' => 'gray',
                        'selesai' => 'success',
                    })
            ])
            ->filters([
                // You can add filters here if needed
            ])
            ->actions([
                Tables\Actions\Action::make('manage_produksi')
                    ->label(fn ($record) => match ($record->status_produksi) {
                        'pending' => 'Mulai Produksi',
                        'proses' => 'Selesaikan Produksi',
                    })
                    ->tooltip(fn ($record) => match ($record->status_produksi) {
                        'pending' => 'Mulai proses produksi',
                        'proses' => 'Tandai produksi selesai',
                        default => '',
                    })
                    ->action(function (Produksi $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                if ($record->status_produksi === 'pending') {
                                    Produksi::calculateAndDeductMaterials($record->produk_jadi_id, $record->jumlah_produksi);
                                    $record->update(['status_produksi' => 'proses']);
                                } else if ($record->status_produksi === 'proses') {
                                    $produkJadi = ProdukJadi::find($record->produk_jadi_id);
                                    $produkJadi->increment('stok', $record->jumlah_produksi);
                                    $record->update([
                                        'status_produksi' => 'selesai',
                                        'tanggal_selesai' => now()->format('Y-m-d'),
                                        'jam_selesai' => now()->format('H:i'),
                                    ]);
                                }
                            });
                        }
                        catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->requiresConfirmation(fn ($record) => $record->status_produksi !== 'selesai')
                    ->color(fn ($record) => match ($record->status_produksi) {
                        'pending' => 'success',
                        'proses' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn ($record) => match ($record->status_produksi) {
                        'pending' => 'heroicon-o-check-circle',
                        'proses' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-ban',
                    })
                    ->visible(fn ($record) => $record->status_produksi !== 'selesai'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProduksis::route('/'),
            'create' => Pages\CreateProduksi::route('/create'),
            'edit' => Pages\EditProduksi::route('/{record}/edit'),
        ];
    }
}
