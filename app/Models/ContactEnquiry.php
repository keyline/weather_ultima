<?php

namespace App\Models;

use Database\Factories\ContactEnquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'subject', 'message'])]
class ContactEnquiry extends Model
{
    /** @use HasFactory<ContactEnquiryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    #[Scope]
    protected function unread(Builder $query): void
    {
        $query->where('is_read', false);
    }

    /**
     * Filter enquiries by a free-text term across the searchable columns.
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $query) use ($term): void {
            foreach (['name', 'email', 'phone', 'subject'] as $column) {
                $query->orWhere($column, 'like', '%'.$term.'%');
            }
        });
    }
}
