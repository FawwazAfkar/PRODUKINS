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
                                Text::make("Monthly production summary report")
                                    ->subtitle(),
                                Text::make("Generated on: " . now()->format("d/m/Y H:i:s"))
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
                        Text::make("Produksi Details")
                            ->fontXl()
                            ->fontBold()
                            ->primary(),
                        Text::make("This is a list of produk created during the selected month")
                            ->fontSm()
                            ->secondary(),
                        Body\Table::make()
                            ->columns([
                                Body\TextColumn::make('tanggal_mulai')
                                    ->label("Start Date")
                                    ->dateTime(),
                                Body\TextColumn::make('tanggal_selesai')
                                    ->label("End Date")
                                    ->dateTime(),
                                Body\TextColumn::make('nama_produk')
                                    ->label("Product Name"),
                                Body\TextColumn::make("jumlah_produksi")
                                    ->label("Quantity Produced"),
                            ])
                            ->data(function (?array $filters) {
                                $dateRange = $filters['production_date'] ?? null;
                                [$from, $to] = $dateRange
                                    ? array_map(
                                        fn($date) => Carbon::parse($date)->startOfDay(),
                                        explode(' - ', $dateRange)
                                    )
                                    : [null, null];
            
                                return Produksi::query()
                                    ->with('produkJadi')
                                    ->when($from, fn($query) => $query->whereDate('tanggal_selesai', '>=', $from))
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

    public function footer(Footer $footer): Footer
    {
        return $footer
            ->schema([
                Text::make("Thank you for reviewing this report."),
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
