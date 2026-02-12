@props(['name', 'placeholder' => '', 'value' => ''])

<div 
    x-data="{
        editor: null,
        showSlashMenu: false,
        slashMenuX: 0,
        slashMenuY: 0,
        previewContent: '',
        showPreview: true,
        initEditor() {
            this.editor = new EasyMDE({
                element: this.$refs.textarea,
                placeholder: '{{ $placeholder }}',
                initialValue: `{!! $value !!}`,
                promptURLs: true,
                autoDownloadFontAwesome: false,
                spellChecker: false,
                status: false,
                minHeight: '150px',
                toolbar: [
                    {
                        name: 'bold',
                        action: EasyMDE.toggleBold,
                        className: 'fa fa-bold',
                        title: 'Negrita',
                    },
                    {
                        name: 'italic',
                        action: EasyMDE.toggleItalic,
                        className: 'fa fa-italic',
                        title: 'Cursiva',
                    },
                    '|',
                    {
                        name: 'quote',
                        action: EasyMDE.toggleBlockquote,
                        className: 'fa fa-quote-left',
                        title: 'Cita',
                    },
                    {
                        name: 'unordered-list',
                        action: EasyMDE.toggleUnorderedList,
                        className: 'fa fa-list-ul',
                        title: 'Lista',
                    },
                    '|',
                    'link',
                    'image',
                    '|',
                    {
                        name: 'html-tag',
                        action: (editor) => {
                            const tag = prompt('Nombre de la etiqueta HTML (ej: div, span, b):', 'div');
                            if (tag) {
                                const cm = editor.codemirror;
                                const selection = cm.getSelection();
                                cm.replaceSelection(`<${tag}>${selection}</${tag}>`);
                                if (!selection) {
                                    const cursor = cm.getCursor();
                                    cm.setCursor(cursor.line, cursor.ch - (tag.length + 3));
                                }
                            }
                        },
                        className: 'fa fa-code',
                        title: 'Insertar Etiqueta Personalizada',
                    },
                    '|',
                    'preview',
                    'side-by-side',
                    'fullscreen',
                    '|',
                    {
                        name: 'guide',
                        action: () => window.open('https://www.markdownguide.org/basic-syntax/', '_blank'),
                        className: 'fa fa-question-circle',
                        title: 'Guía de Markdown',
                    }
                ]
            });

            // Initial preview sync
            this.previewContent = this.editor.options.previewRender(this.editor.value());

            // Sync with hidden textarea and preview
            this.editor.codemirror.on('change', () => {
                const val = this.editor.value();
                this.$refs.textarea.value = val;
                this.previewContent = this.editor.options.previewRender(val);
            });

            // Slash Menu Logic
            this.editor.codemirror.on('keyup', (cm, event) => {
                const cursor = cm.getCursor();
                const line = cm.getLine(cursor.line);
                const lastChar = line.charAt(cursor.ch - 1);

                if (lastChar === '/') {
                    const coords = cm.cursorCoords(true, 'page');
                    const editorRect = this.$el.getBoundingClientRect();
                    
                    this.slashMenuX = coords.left - editorRect.left;
                    this.slashMenuY = coords.bottom - editorRect.top;
                    this.showSlashMenu = true;
                } else if (event.key === 'Escape' || event.key === ' ') {
                    this.showSlashMenu = false;
                }
            });

            // Ensure sync before form submission
            const form = this.$refs.textarea.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    this.$refs.textarea.value = this.editor.value();
                });
            }
        },
        insertCommand(opening, closing = '') {
            const cm = this.editor.codemirror;
            const cursor = cm.getCursor();
            
            cm.replaceRange('', {line: cursor.line, ch: cursor.ch - 1}, cursor);
            
            if (closing) {
                const selection = cm.getSelection();
                cm.replaceSelection(opening + selection + closing);
            } else {
                cm.replaceSelection(opening);
            }
            cm.focus();
            this.showSlashMenu = false;
        }
    }"
    x-init="setTimeout(() => initEditor(), 100)"
    class="relative group"
>
    <!-- Editor Container -->
    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm focus-within:ring-2 focus-within:ring-[#22A9C8]/20 transition-all">
        <textarea x-ref="textarea" name="{{ $name }}" class="hidden"></textarea>
    </div>

    <!-- Live Preview Section -->
    <div x-show="showPreview" x-cloak class="mt-4">
        <div class="flex items-center justify-between mb-2 px-1">
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fa fa-eye"></i>
                Vista Previa en Tiempo Real
                
                <!-- Info Tooltip -->
                <div x-data="{ showInfo: false }" class="relative inline-block ml-1">
                    <button 
                        type="button" 
                        @mouseenter="showInfo = true" 
                        @mouseleave="showInfo = false"
                        @click="showInfo = !showInfo"
                        class="text-gray-300 hover:text-[#22A9C8] transition-colors focus:outline-none"
                    >
                        <i class="fa fa-info-circle text-xs"></i>
                    </button>
                    
                    <div 
                        x-show="showInfo" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute bottom-full left-0 mb-3 w-72 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.2)] border border-gray-100 dark:border-gray-700 z-[110] pointer-events-none"
                        x-cloak
                    >
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center gap-2 text-[#22A9C8]">
                                <i class="fa fa-code text-xs"></i>
                                <span class="text-[11px] font-bold uppercase tracking-wider">Uso de HTML</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                Puedes usar etiquetas estándar para un diseño más flexible. La vista previa renderizará el resultado final.
                            </p>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3 border border-gray-100 dark:border-gray-800">
                                <p class="text-[10px] text-gray-400 mb-1.5 font-bold uppercase tracking-tighter">Ejemplo:</p>
                                <code class="text-[10px] text-pink-500 break-all leading-relaxed bg-pink-50 dark:bg-pink-900/20 px-1 py-0.5 rounded">&lt;span style="color: #22A9C8; font-weight: bold"&gt;¡Importante!&lt;/span&gt;</code>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-400 italic">
                                <span>Resultado:</span>
                                <span style="color: #22A9C8; font-weight: bold">¡Importante!</span>
                            </div>
                        </div>
                        <!-- Arrow -->
                        <div class="absolute top-full left-4 -translate-y-1/2 w-3 h-3 bg-white dark:bg-gray-800 border-r border-b border-gray-100 dark:border-gray-700 rotate-45"></div>
                    </div>
                </div>
            </h4>
            <span class="text-[9px] text-gray-400 italic">Se muestra como aparecerá en la tarea</span>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 border border-dashed border-gray-200 dark:border-gray-700 min-h-[100px]">
            <div class="prose prose-sm max-w-none dark:prose-invert prose-task-description text-gray-700 dark:text-gray-300" x-html="previewContent || '<span class=\'text-gray-400 italic\'>Empieza a escribir para ver la vista previa...</span>'"></div>
        </div>
    </div>

    <!-- Slash Menu Dropdown -->
    <div 
        x-show="showSlashMenu" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        @click.away="showSlashMenu = false"
        :style="`left: ${slashMenuX}px; top: ${slashMenuY + 8}px;`"
        class="absolute z-[100] w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 dark:border-gray-700 py-2.5 overflow-hidden"
        x-cloak
    >
        <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-gray-700 mb-1.5 flex justify-between items-center">
            <span>Comandos Rápidos</span>
            <kbd class="px-1.5 py-0.5 rounded bg-gray-50 dark:bg-gray-700 text-[9px] font-mono lowercase tracking-normal">esc</kbd>
        </div>
        
        <div class="px-2 space-y-0.5 max-h-64 overflow-y-auto custom-scrollbar">
            <button type="button" @click="insertCommand('# ')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-black shadow-sm group-hover/item:scale-110 transition-transform">H1</div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Título Principal</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Formato de encabezado grande</div>
                </div>
            </button>
            
            <button type="button" @click="insertCommand('## ')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-black shadow-sm group-hover/item:scale-110 transition-transform">H2</div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Subtítulo</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Título de sección mediana</div>
                </div>
            </button>
            
            <button type="button" @click="insertCommand('- ')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-purple-50/50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg shadow-sm group-hover/item:scale-110 transition-transform">
                    <i class="fa fa-list-ul text-[10px]"></i>
                </div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Lista de viñetas</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Lista desordenada simple</div>
                </div>
            </button>

            <button type="button" @click="insertCommand('1. ')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-amber-50/50 dark:hover:bg-amber-900/20 hover:text-amber-600 dark:hover:text-amber-400 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-black shadow-sm group-hover/item:scale-110 transition-transform">1.</div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Lista numerada</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Pasos o secuencia de ítems</div>
                </div>
            </button>

            <button type="button" @click="insertCommand('<div>\n\n</div>')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-lg shadow-sm group-hover/item:scale-110 transition-transform">
                    <div class="text-[10px] font-bold">&lt;div&gt;</div>
                </div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Contenedor HTML</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Insertar etiqueta &lt;div&gt;</div>
                </div>
            </button>

            <button type="button" @click="insertCommand(\'<span style=\\\'color: #22A9C8; font-weight: bold;\\\'>\', \'</span>\')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-cyan-50/50 dark:hover:bg-cyan-900/20 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 rounded-lg shadow-sm group-hover/item:scale-110 transition-transform">
                    <div class="text-[10px] font-bold">CLR</div>
                </div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Texto Color</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Resalta texto con color</div>
                </div>
            </button>

            <button type="button" @click="insertCommand(\'<br>\')" class="w-full flex items-center gap-3.5 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 rounded-xl transition-all group/item">
                <div class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg shadow-sm group-hover/item:scale-110 transition-transform">
                    <div class="text-[10px] font-bold">BR</div>
                </div>
                <div class="flex-1 text-left">
                    <div class="font-semibold leading-none mb-1">Salto de línea</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Insertar etiqueta &lt;br&gt;</div>
                </div>
            </button>
        </div>
    </div>

    <!-- Formatting Guide Button -->
    <div class="absolute bottom-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none group-focus-within:opacity-100">
        <button 
            type="button"
            onclick="window.open('https://www.markdownguide.org/cheat-sheet/', '_blank')"
            class="pointer-events-auto px-2.5 py-1.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur hover:bg-white dark:hover:bg-gray-700 text-[9px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 rounded-lg transition-all border border-gray-200 dark:border-gray-700 flex items-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5"
        >
            <div class="w-4 h-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <i class="fa fa-question text-[7px]"></i>
            </div>
            Guía de Formato
        </button>
    </div>
</div>
