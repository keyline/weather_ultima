{{-- Shared enquiry detail modal. Expects $fields = ['payloadKey' => 'Label', ...] --}}
<div id="enquiry-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-950/50" data-modal-close></div>
    <div class="relative z-10 flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-lg bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-4">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Enquiry #<span data-field="id"></span></p>
                <h3 class="mt-1 truncate text-lg font-bold text-slate-900" data-field="{{ array_key_first($fields) }}"></h3>
            </div>
            <button type="button" data-modal-close aria-label="Close" class="shrink-0 rounded p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="flex-1 space-y-5 overflow-y-auto px-6 py-5">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ($fields as $key => $label)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                        <dd class="mt-0.5 break-words text-slate-700" data-field="{{ $key }}"></dd>
                    </div>
                @endforeach
            </dl>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Message</dt>
                <div class="mt-1.5 rounded bg-slate-50 p-4">
                    <p class="whitespace-pre-line leading-7 text-slate-700" data-field="message"></p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4">
            <button type="button" data-modal-close class="admin-btn admin-btn--ghost admin-btn--sm">Close</button>
            <form method="POST" data-delete-form onsubmit="return confirm('Delete this enquiry permanently? This cannot be undone.');">
                @csrf
                @method ('DELETE')
                <button class="admin-btn admin-btn--danger admin-btn--sm">
                    <i class="fa-solid fa-trash-can"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>
