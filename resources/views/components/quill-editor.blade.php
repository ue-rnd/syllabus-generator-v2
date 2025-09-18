@props([
    'wireModel' => '',
    'placeholder' => 'Start typing...',
    'toolbar' => 'basic',
    'rows' => 4,
    'errorClass' => '',
    'initialContent' => ''
])

@php
    $editorId = 'quill-editor-' . uniqid();
    $toolbarConfig = match($toolbar) {
        'minimal' => '[["bold", "italic", "underline"], [{"list": "bullet"}]]',
        'basic' => '[["bold", "italic", "underline", "strike"], ["link"], [{"list": "ordered"}, {"list": "bullet"}]]',
        'full' => '[["bold", "italic", "underline", "strike"], ["link"], [{"list": "ordered"}, {"list": "bullet"}], ["blockquote", "code-block"]]',
        default => '[["bold", "italic", "underline", "strike"], ["link"], [{"list": "ordered"}, {"list": "bullet"}]]'
    };
@endphp

<div {{ $attributes->merge(['class' => 'quill-editor-wrapper']) }} 
     wire:ignore
     x-data="{
         quill: null,
         wireModel: '{{ $wireModel }}',
         editorId: '{{ $editorId }}',
         toolbar: {{ $toolbarConfig }},
         placeholder: '{{ $placeholder }}',
         isInitialized: false,
         init() {
             if (this.isInitialized) return;
             this.isInitialized = true;
             
             this.$nextTick(() => {
                 try {
                     this.quill = new Quill('#' + this.editorId, {
                         theme: 'snow',
                         placeholder: this.placeholder,
                         modules: {
                             toolbar: this.toolbar
                         }
                     });
                     
                     // Set initial content from Livewire
                     const initialContent = @this.get('{{ $wireModel }}') || '{{ addslashes($initialContent) }}';
                     if (initialContent && initialContent.trim() !== '') {
                         this.quill.root.innerHTML = initialContent;
                     }
                     
                     // Update Livewire only when editor loses focus (lazy update)
                     this.quill.on('selection-change', (range) => {
                         if (range === null) {
                             // Editor lost focus, update Livewire
                             const content = this.quill.root.innerHTML;
                             @this.set(this.wireModel, content);
                         }
                     });
                     
                     // Also update on blur event for better reliability
                     this.quill.root.addEventListener('blur', () => {
                         const content = this.quill.root.innerHTML;
                         @this.set(this.wireModel, content);
                     });
                     
                    // Watch for Livewire property changes and update editor content
                    this.$watch(() => @this.get(this.wireModel), (newValue) => {
                        if (this.quill && newValue !== this.quill.root.innerHTML) {
                            this.quill.root.innerHTML = newValue || '';
                        }
                    });
                     
                     // Listen for step navigation events to refresh content
                     this.$wire.on('step-changed', () => {
                         setTimeout(() => {
                             const currentValue = @this.get('{{ $wireModel }}');
                             if (this.quill && currentValue !== this.quill.root.innerHTML) {
                                 this.quill.root.innerHTML = currentValue || '';
                             }
                         }, 100);
                     });
                     
                 } catch (error) {
                     console.error('Quill initialization error:', error);
                 }
             });
         }
     }">
    <div id="{{ $editorId }}" 
         class="quill-editor border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-red-500 focus-within:border-transparent {{ $errorClass }}"
         style="min-height: {{ $rows * 1.5 }}rem;">
    </div>
</div>