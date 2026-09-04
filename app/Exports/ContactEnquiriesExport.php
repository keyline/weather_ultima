<?php

namespace App\Exports;

use App\Models\ContactEnquiry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContactEnquiriesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * @param  Collection<int, ContactEnquiry>  $enquiries
     */
    public function __construct(private readonly Collection $enquiries) {}

    /**
     * @return Collection<int, ContactEnquiry>
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
        return ['ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Submitted At'];
    }

    /**
     * @param  ContactEnquiry  $enquiry
     * @return list<mixed>
     */
    public function map($enquiry): array
    {
        return [
            $enquiry->id,
            $enquiry->name,
            $enquiry->email,
            $enquiry->phone,
            $enquiry->subject,
            $enquiry->message,
            $enquiry->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
