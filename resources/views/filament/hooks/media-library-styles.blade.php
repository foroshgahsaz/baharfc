<style>
    /* استایل پایدار کتابخانه — حتی اگر theme.css قدیمی باشد اعمال می‌شود */
    .baharfc-media-picker-overlay{position:fixed;inset:0;z-index:10050;display:flex;align-items:center;justify-content:center;padding:1.25rem;background:rgba(15,23,42,.58)}
    .baharfc-media-picker-dialog{display:flex;flex-direction:column;width:min(1120px,100%);height:min(86vh,780px);overflow:hidden;background:#fff;border-radius:1.1rem;box-shadow:0 25px 50px -12px rgba(0,0,0,.4)}
    .dark .baharfc-media-picker-dialog{background:#111827;color:#f9fafb}
    .baharfc-media-picker-header,.baharfc-media-picker-toolbar{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.9rem 1.1rem;border-bottom:1px solid #e5e7eb}
    .dark .baharfc-media-picker-header,.dark .baharfc-media-picker-toolbar{border-color:#1f2937}
    .baharfc-media-picker-title{margin:0;font-size:1.05rem;font-weight:700}
    .baharfc-media-picker-close{display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border:0;border-radius:999px;background:transparent;color:#6b7280;font-size:1.5rem;line-height:1;cursor:pointer}
    .baharfc-media-picker-search{width:100%;border:1px solid #e5e7eb;border-radius:.7rem;padding:.55rem .85rem;font-size:.875rem;background:#fff}
    .dark .baharfc-media-picker-search{border-color:#374151;background:#111827;color:#f9fafb}
    .baharfc-media-picker-body{flex:1 1 auto;overflow:auto;padding:1rem}
    .baharfc-media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(128px,1fr));gap:.75rem}
    .baharfc-media-card{position:relative;display:flex;flex-direction:column;width:100%;margin:0;padding:0;overflow:hidden;border:1px solid #e5e7eb;border-radius:.85rem;background:#fff;text-align:right;cursor:pointer;appearance:none}
    .baharfc-media-card:hover,.baharfc-media-card:focus-visible{outline:none;border-color:#14b8a6;box-shadow:0 0 0 2px rgba(20,184,166,.35)}
    .dark .baharfc-media-card{background:#111827;border-color:#374151}
    .baharfc-media-thumb{position:relative;width:100%;aspect-ratio:1/1;overflow:hidden;background:#f3f4f6}
    .dark .baharfc-media-thumb{background:#1f2937}
    .baharfc-media-thumb img{position:absolute;inset:0;display:block;width:100%!important;height:100%!important;max-width:100%!important;max-height:100%!important;object-fit:cover!important}
    .baharfc-media-thumb-fallback{display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:1.75rem}
    .baharfc-media-caption{margin:0;padding:.4rem .5rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px;color:#4b5563}
    .baharfc-media-empty{grid-column:1/-1;padding:3rem 1rem;text-align:center;color:#6b7280;font-size:.875rem}
</style>
