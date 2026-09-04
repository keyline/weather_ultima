<?php

namespace App\Exports;

use App\Models\ProductEnquiry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductEnquiriesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * @param  Collection<int, ProductEnquiry>  $enquiries
     */
    public function __construct(private readonly Collection $enquiries) {}

    /**
     * @return Collection<int, ProductEnquiry>
     */
    public function collection(): Collection
    {
        return $this->enquiries;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return ['ID', 'Product', 'Name', 'Email', 'Phone', 'Message', 'Submitted At'];
    }

    /**
     * @param  ProductEnquiry  $enquiry
     * @return list<mixed>
     */
    public function map($enquiry): array
    {
        return [
            $enquiry->id,
            $enquiry->product_name,
            $enquiry->name,
            $enquiry->email,
            $enquiry->phone,
            $enquiry->message,
            $enquiry->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
