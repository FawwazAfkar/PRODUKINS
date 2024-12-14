<?php

namespace App\Filament\Reports;

use App\Models\Produksi;
use App\Models\ProdukJadi;
use EightyNine\Reports\Components\Image;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\VerticalSpace;
use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use Filament\Forms\Form;
use Carbon\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class ProduksiReport extends Report
{
    protected static ?string $title = 'Rekap Produksi';
    protected static ?string $slug = 'rekap-produksi';
    protected static ?string $navigationGroup = 'Rekap';

    public function header(Header $header): Header
    {
        $imagePath = asset('img/fr-logo.png');

        return $header
            ->schema([
                Header\Layout\HeaderRow::make()
                    ->schema([
                        Header\Layout\HeaderColumn::make()
                            ->schema([
                                Image::make($imagePath)
                                    ->width9Xl(),
                            ])->alignLeft(),
                        Header\Layout\HeaderColumn::make()
                            ->schema([
                                Text::make("Produksi Report")
                                    ->title()
                                    ->primary(),
                                Text::make("Report Produksi Bulanan")
                                    ->subtitle(),
                                Text::make("Dibuat pada: " . now()->format("d/m/Y H:i:s"))
                                    ->subtitle(),
                            ])->alignRight(),
                    ]),
            ]);
    }

    public function body(Body $body): Body
    {
        return $body
            ->schema([
                Body\Layout\BodyColumn::make()
                    ->schema([
                        Text::make("Detail Produksi")
                            ->fontXl()
                            ->fontBold()
                            ->primary(),
                        Text::make("Berikut ini merupakan list produksi yang telah dilakukan.")
                            ->fontSm()
                            ->secondary(),
                        Body\Table::make()
                            ->columns([
                                Body\TextColumn::make('tanggal_mulai')
                                    ->label("Start Date"),
                                Body\TextColumn::make('tanggal_selesai')
                                    ->label("End Date"),
                                Body\TextColumn::make('nama_produk')
                                    ->label("Product Name"),
                                Body\TextColumn::make("jumlah_produksi")
                                    ->label("Quantity Produced"),
                            ])
                            ->data(function (?array $filters) {
                                $dateRange = $filters['production_date'] ?? null;
                                $dateFormat = 'Y-m-d';

                                // Safely parse dates
                                try {
                                    [$from, $to] = $dateRange
                                        ? array_map(
                                            fn($date) => Carbon::createFromFormat($dateFormat, trim($date))->startOfDay(),
                                            explode(' - ', $dateRange)
                                        )
                                        : [null, null];
                                } catch (\Exception $e) {
                                    logger()->error('Failed to parse date range', ['error' => $e->getMessage(), 'input' => $dateRange]);
                                    $from = $to = null; // Fallback if parsing fails
                                }

                                return Produksi::query()
                                    ->with('produkJadi')
                                    ->when($from, fn($query) => $query->whereDate('tanggal_mulai', '>=', $from))
                                    ->when($to, fn($query) => $query->whereDate('tanggal_selesai', '<=', $to))
                                    ->get()
                                    ->map(function ($record) {
                                        $record->nama_produk = $record->produkJadi->nama_produk ?? 'Unknown';
                                        return $record;
                                    });
                            }),
                        VerticalSpace::make(),
                    ]),
            ]);
    }

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                DateRangePicker::make("production_date")
                    ->label("Production Date")
                    ->placeholder("Select a date range"),
            ]);
    }
}
