@once
    @push ('styles')
        <style>
            .wx-thanks .modal-content {
                border: 0;
                border-radius: 22px;
                overflow: hidden;
                box-shadow: 0 30px 80px -30px rgba(11, 55, 109, 0.55);
            }
            .wx-thanks-body {
                text-align: center;
                padding: 44px 34px 38px;
            }
            .wx-thanks-badge {
                width: 84px;
                height: 84px;
                margin: 0 auto 22px;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: radial-gradient(circle at 30% 30%, #59bfe5, #0b376d);
                color: #fff;
                font-size: 34px;
                animation: wxThanksPop 0.45s cubic-bezier(0.18, 1.25, 0.4, 1) both;
            }
            .wx-thanks-badge i {
                animation: wxThanksTick 0.4s ease 0.2s both;
            }
            .wx-thanks-title {
                font-size: 1.6rem;
                font-weight: 800;
                color: #0b376d;
                margin-bottom: 10px;
            }
            .wx-thanks-text {
                color: #5b6b82;
                line-height: 1.6;
                margin-bottom: 26px;
            }
            .wx-thanks-close {
                border: 0;
                border-radius: 999px;
                padding: 12px 34px;
                font-weight: 700;
                color: #fff;
                background: #0b376d;
                transition: background 0.2s ease, transform 0.2s ease;
            }
            .wx-thanks-close:hover {
                background: #092d59;
                transform: translateY(-1px);
            }
            .wx-thanks-accent {
                height: 6px;
                background: linear-gradient(90deg, #0b376d, #fbbf24);
            }
            @keyframes wxThanksPop {
                from { transform: scale(0.4); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            @keyframes wxThanksTick {
                from { transform: scale(0); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
        </style>
    @endpush
@endonce

<div class="modal fade wx-thanks" id="wxThankYouModal" tabindex="-1" aria-labelledby="wxThankYouTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="wx-thanks-accent"></div>
            <div class="wx-thanks-body">
                <div class="wx-thanks-badge"><i class="fa-solid fa-check"></i></div>
                <h3 class="wx-thanks-title" id="wxThankYouTitle">Thank You!</h3>
                <p class="wx-thanks-text" id="wxThankYouText">
                    Your enquiry has been submitted successfully. Our team will get back to you shortly.
                </p>
                <button type="button" class="wx-thanks-close" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
