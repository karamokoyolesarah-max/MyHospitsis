<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Documents Médicaux') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data='fileManager(@json($folders))'>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg min-h-[500px] flex flex-col border border-gray-200">
                
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2 overflow-x-auto">
                        <button @click="resetPath()" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
                            <i class="fas fa-home"></i>
                        </button>
                        
                        <span class="text-gray-400" x-show="currentPath.length > 0"><i class="fas fa-chevron-right text-xs"></i></span>

                        <template x-for="(crumb, index) in currentPath" :key="index">
                            <div class="flex items-center">
                                <button @click="goToLevel(index)" 
                                        class="px-3 py-1 rounded-md font-medium text-sm transition-colors hover:bg-blue-100 hover:text-blue-700 whitespace-nowrap"
                                        :class="index === currentPath.length - 1 ? 'text-gray-900 font-bold bg-white shadow-sm' : 'text-gray-600'"
                                        x-text="crumb">
                                </button>
                                <span class="mx-2 text-gray-400" x-show="index < currentPath.length - 1">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            </div>
                        </template>

                        <div x-show="currentPath.length === 0" class="text-gray-400 italic text-sm ml-2 whitespace-nowrap">
                            Espace Documents
                        </div>
                    </div>

                    <!-- Barre de Recherche -->
                    <div class="relative max-w-[200px] md:max-w-xs w-full ml-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                        </div>
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Rechercher..." 
                               class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-xl leading-5 bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all hover:border-gray-400">
                    </div>
                </div>

                <div class="p-0 flex-1 bg-white">
                    <!-- En-têtes style Explorateur -->
                    <div class="grid grid-cols-12 gap-4 px-6 py-2 bg-gray-50 border-b border-gray-100 text-[10px] uppercase font-black text-gray-400 tracking-widest">
                        <div class="col-span-8 md:col-span-6">Nom</div>
                        <div class="hidden md:block col-span-3">Détails</div>
                        <div class="col-span-4 md:col-span-3 text-right">Action</div>
                    </div>

                    <!-- Vue Dossiers (Style Liste) -->
                    <div x-show="viewType === 'folders'" class="divide-y divide-gray-50">
                        <template x-for="(item, key) in filteredFolders" :key="key">
                            <div @click="enterFolder(key)" 
                                 class="grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-blue-50/50 cursor-pointer transition-colors group">
                                
                                <div class="col-span-8 md:col-span-6 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm border border-gray-100 bg-white"
                                         :class="getColorClass(key, item)"
                                         :style="item.color && item.color.startsWith('#') ? 'background-color: ' + item.color + '11; color: ' + item.color : ''">
                                        <i :class="getIcon(key, item)" class="text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-sm group-hover:text-blue-600 transition-colors" x-text="key"></h3>
                                        <p class="md:hidden text-[10px] text-gray-500 font-medium uppercase tracking-tight" x-text="getSubLabel(item)"></p>
                                    </div>
                                </div>

                                <div class="hidden md:block col-span-3">
                                    <span class="text-xs text-gray-500 font-medium" x-text="getSubLabel(item)"></span>
                                </div>

                                <div class="col-span-4 md:col-span-3 text-right">
                                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-400 transition-colors"></i>
                                </div>
                            </div>
                        </template>

                        <div x-show="Object.keys(filteredFolders).length === 0" class="py-20 text-center text-gray-400">
                            <i class="fas fa-folder-open text-4xl mb-3 opacity-20"></i>
                            <p class="text-sm font-medium">Aucun élément ne correspond à votre recherche.</p>
                        </div>
                    </div>

                    <!-- Vue Fichiers (Style Liste harmonisée) -->
                    <div x-show="viewType === 'files'" class="divide-y divide-gray-50">
                        <template x-for="(group, date) in filteredGroupedFiles" :key="date">
                            <div>
                                <div class="px-6 py-2 bg-gray-50/50 group flex items-center gap-2">
                                    <i class="far fa-calendar-alt text-gray-300 text-xs"></i>
                                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="formatDate(date)"></h3>
                                </div>
                                
                                <div class="divide-y divide-gray-50">
                                    <template x-for="doc in group" :key="doc.id">
                                        <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-blue-50/30 transition-colors group bg-white">
                                            
                                            <div class="col-span-8 md:col-span-6 flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm border border-blue-100 group-hover:scale-110 transition-transform">
                                                    <i :class="doc.icon || 'fas fa-file-alt'" class="text-xl"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-blue-700 transition-colors" x-text="doc.title"></h4>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                                                            <i class="far fa-clock mr-1"></i>
                                                            <span x-text="new Date(doc.date).toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'})"></span>
                                                        </span>
                                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded text-[9px] text-gray-500 font-black uppercase tracking-tighter" x-text="doc.type"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hidden md:block col-span-3">
                                                <span x-show="doc.result_text" class="text-[10px] bg-purple-100 text-purple-600 px-2 py-0.5 rounded-full font-black uppercase tracking-tighter border border-purple-200">RÉSULTAT PRÊT</span>
                                                <span x-show="!doc.result_text && doc.download_route" class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-black uppercase tracking-tighter border border-blue-200">DOC. PDF</span>
                                                <span x-show="!doc.result_text && !doc.download_route" class="text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full font-black uppercase tracking-tighter border border-orange-200 animate-pulse">EN COURS</span>
                                            </div>

                                            <div class="col-span-4 md:col-span-3 flex justify-end gap-2">
                                                <template x-if="doc.result_text">
                                                    <button @click="openResult(doc)" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Voir Résultat">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </template>

                                                <template x-if="doc.download_route">
                                                    <a :href="doc.download_route" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Télécharger">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </template>

                                                <button @click="shareFile(doc)" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors" title="Partager">
                                                    <i class="fas fa-share-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="Object.values(filteredGroupedFiles).flat().length === 0" class="py-20 text-center text-gray-400">
                            <i class="fas fa-file-medical text-4xl mb-3 opacity-20"></i>
                            <p class="text-sm font-medium">Aucun document ne correspond à votre recherche.</p>
                        </div>
                    </div>
                </div>

            <!-- Modal Résultat -->
            <div x-show="showResultModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showResultModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showResultModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showResultModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-flask text-purple-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="currentResultTitle"></h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-2">Résultat :</p>
                                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 whitespace-pre-line" x-text="currentResultText"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm" @click="showResultModal = false">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

                <div class="bg-gray-50 border-t border-gray-200 px-6 py-3 text-xs text-gray-500 flex justify-between items-center">
                    <span>
                        <span class="font-bold" x-text="viewType === 'folders' ? Object.keys(filteredFolders).length : filteredFilesList.length"></span> éléments trouvés
                        <template x-if="searchQuery">
                            <span class="text-blue-500 ml-1">(filtré par recherche)</span>
                        </template>
                    </span>
                    <span class="font-medium uppercase tracking-tighter opacity-50" x-text="currentPath.join(' / ') || '/'"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('fileManager', (initialData) => ({
                data: initialData,
                currentPath: [],
                viewType: 'folders',
                showResultModal: false,
                currentResultText: '',
                currentResultTitle: '',
                
                init() {
                    console.log('FileManager initializing...', this.data);
                    if (typeof this.data === 'string') {
                        try {
                            this.data = JSON.parse(this.data);
                        } catch (e) {
                            console.error('Failed to parse initialData', e);
                            this.data = {};
                        }
                    }
                    if (!this.data) this.data = {};
                },

                openResult(doc) {
                    this.currentResultTitle = doc.title.replace(' (En cours)', '');
                    this.currentResultText = doc.result_text;
                    this.showResultModal = true;
                },

                get currentLevelItems() {
                    let current = this.data;
                    for (let folder of this.currentPath) {
                        if (!current || !current[folder]) return {};
                        current = current[folder].children ? current[folder].children : current[folder];
                    }
                    
                    if (Array.isArray(current)) {
                        this.viewType = 'files';
                        return {};
                    }
                    
                    this.viewType = 'folders';
                    return current || {};
                },

                get filteredFolders() {
                    const items = this.currentLevelItems;
                    if (!this.searchQuery) return items;
                    
                    const filtered = {};
                    const query = this.searchQuery.toLowerCase();
                    
                    Object.keys(items).forEach(key => {
                        if (key.toLowerCase().includes(query)) {
                            filtered[key] = items[key];
                        }
                    });
                    return filtered;
                },

                get filesList() {
                    let current = this.data;
                    for (let folder of this.currentPath) {
                        if (!current[folder]) return [];
                        current = current[folder].children ? current[folder].children : current[folder];
                    }
                    return Array.isArray(current) ? current : [];
                },

                get filteredFilesList() {
                    const current = this.filesList;
                    if (!this.searchQuery) return current;
                    
                    const query = this.searchQuery.toLowerCase();
                    return current.filter(doc => doc.title.toLowerCase().includes(query));
                },

                get filteredGroupedFiles() {
                    const current = this.filteredFilesList;
                    const grouped = {};
                    current.forEach(doc => {
                       const dateObj = new Date(doc.date);
                       const d = dateObj.toISOString().split('T')[0]; 
                       if (!grouped[d]) grouped[d] = [];
                       grouped[d].push(doc);
                    });
                    return Object.keys(grouped).sort().reverse().reduce((obj, key) => {
                        obj[key] = grouped[key];
                        return obj;
                    }, {});
                },

                enterFolder(key) {
                    this.currentPath.push(key);
                },

                goToLevel(index) {
                    this.currentPath = this.currentPath.slice(0, index + 1);
                },

                resetPath() {
                    this.currentPath = [];
                },

                formatDate(dateString) {
                    if(!dateString) return '';
                    return new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
                },

                getIcon(key, item = null) {
                    if (item && item.icon) return item.icon;
                    const k = key.toLowerCase();
                    if (k.includes('maison')) return 'fas fa-home';
                    if (k.includes('hôpital')) return 'fas fa-hospital-alt';
                    if (k.includes('admission')) return 'fas fa-bed';
                    if (k.includes('lab')) return 'fas fa-flask';
                    if (k.includes('test')) return 'fas fa-vial'; // Icone pour Test
                    return 'fas fa-folder';
                },

                getSubLabel(item) {
                    if (!item) return '0 élément';
                    if (Array.isArray(item)) return item.length + ' documents';
                    let target = item.children ? item.children : item;
                    return Object.keys(target).length + ' éléments';
                },

                getColorClass(key, item) {
                    if (!item || (item.color && item.color.startsWith('#'))) return '';
                    if (item.color === 'blue') return 'bg-blue-100 text-blue-600';
                    if (item.color === 'green') return 'bg-green-100 text-green-600';
                    if (item.color === 'red') return 'bg-red-100 text-red-600';
                    return 'bg-indigo-100 text-indigo-600';
                },

                async shareFile(doc) {
                    if (navigator.share) {
                        try {
                            await navigator.share({
                                title: doc.title,
                                text: 'Document partagé depuis mon Espace Patient',
                                url: doc.download_route || window.location.href // Fallback si pas de fichier
                            });
                        } catch (err) {
                            console.log('Share canceled', err);
                        }
                    } else {
                        // Fallback: Copy link
                        if (doc.download_route) {
                            navigator.clipboard.writeText(doc.download_route);
                            alert('Lien copié dans le presse-papier !');
                        } else {
                            alert('Fichier non disponible pour le partage.');
                        }
                    }
                }
            }));
        });
    </script>
</x-portal-layout>