<style>
    /* Shared form styles for Sales and Expenses */
    .arabic-text {
        font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        direction: rtl;
        text-align: right;
    }

    /* Modern Form Section */
    .form-section {
        background: var(--ct-card-bg);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid var(--ct-border-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .form-section::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, var(--ct-primary), var(--ct-info));
    }
    .form-section:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    /* Dark Mode Enhancements */
    [data-bs-theme="dark"] .form-section {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    [data-bs-theme="dark"] .form-section:hover {
        background: rgba(255, 255, 255, 0.04);
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }

    /* Section Headers */
    .section-header {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ct-heading-color);
    }
    .section-header iconify-icon {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    /* Form Controls Enhancement */
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 16px;
        border: 1.5px solid var(--ct-border-color);
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--ct-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--ct-primary-rgb), 0.15);
        transform: translateY(-1px);
    }
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: var(--ct-body-color);
    }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: var(--ct-primary);
    }

    /* Form Labels */
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--ct-body-color);
        font-size: 0.9rem;
    }

    /* Payment Method Cards */
    .payment-card {
        background: var(--ct-card-bg);
        border: 2px solid var(--ct-border-color);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
    }
    .payment-card:hover {
        border-color: var(--ct-primary);
        background: rgba(var(--ct-primary-rgb), 0.05);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .payment-card input[type="radio"]:checked ~ .payment-content {
        color: var(--ct-primary);
    }
    .payment-card input[type="radio"]:checked ~ * .payment-icon {
        color: var(--ct-primary);
        transform: scale(1.1);
    }
    .payment-card.active {
        border-color: var(--ct-primary);
        background: rgba(var(--ct-primary-rgb), 0.1);
        box-shadow: 0 4px 12px rgba(var(--ct-primary-rgb), 0.2);
    }
    .payment-icon {
        font-size: 2rem;
        transition: all 0.3s ease;
    }
    .payment-content { flex: 1; }
    .payment-title { font-weight: 600; font-size: 1rem; margin-bottom: 2px; }
    .payment-desc { font-size: 0.75rem; opacity: 0.7; }
    [data-bs-theme="dark"] .payment-card { background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.1); }
    [data-bs-theme="dark"] .payment-card:hover { background: rgba(255, 255, 255, 0.06); }
    [data-bs-theme="dark"] .payment-card.active { background: rgba(var(--ct-primary-rgb), 0.15); }

    /* Calculation Cards */
    .calc-card {
        background: var(--ct-card-bg);
        border: 2px solid var(--ct-border-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }
    .calc-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .calc-card.info { border-color: var(--ct-info); background: rgba(var(--ct-info-rgb), 0.05); }
    .calc-card.success { border-color: var(--ct-success); background: rgba(var(--ct-success-rgb), 0.05); }
    .calc-card.warning { border-color: var(--ct-warning); background: rgba(var(--ct-warning-rgb), 0.05); }
    .calc-label { font-size: 0.85rem; font-weight: 600; opacity: 0.8; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .calc-value { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .calc-icon { font-size: 2.5rem; opacity: 0.3; margin-bottom: 8px; }
    [data-bs-theme="dark"] .calc-card { background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.1); }
    [data-bs-theme="dark"] .calc-card.info { background: rgba(var(--ct-info-rgb), 0.1); }
    [data-bs-theme="dark"] .calc-card.success { background: rgba(var(--ct-success-rgb), 0.1); }
    [data-bs-theme="dark"] .calc-card.warning { background: rgba(var(--ct-warning-rgb), 0.1); }

    /* Buttons */
    .btn { padding: 12px 28px; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }
    .btn iconify-icon { font-size: 1.2rem; }

    /* Responsive Improvements */
    @media (max-width: 768px) {
        .form-section { padding: 16px; margin-bottom: 16px; }
        .section-header { font-size: 1rem; }
        .payment-card { padding: 12px; margin-bottom: 12px; }
        .calc-value { font-size: 1.2rem; }
        .calc-icon { font-size: 2rem; }
        .btn { padding: 10px 20px; width: 100%; justify-content: center; }
    }

    /* Loading State */
    .form-control.loading {
        background-image: linear-gradient(90deg, transparent, rgba(var(--ct-primary-rgb), 0.1), transparent);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    @keyframes loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    /* Smooth transitions for show/hide */
    .fade-in { animation: fadeIn 0.3s ease-in; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
