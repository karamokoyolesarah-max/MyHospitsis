<div id="tab-financial-monitoring" class="tab-pane animate-in zoom-in-95 duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 text-left gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Monitoring des Flux Financiers</h2>
            <p class="text-slate-500 font-medium">Répartition des revenus entre activation, commissions et abonnements.</p>
        </div>
        <div class="flex gap-4">
            <button onclick="refreshFinancialData()" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-4 rounded-2xl font-bold transition shadow-xl shadow-slate-200 flex items-center justify-center gap-3">
                <i class="bi bi-arrow-clockwise"></i>
                Actualiser
            </button>
            <button onclick="openTestRechargeModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-4 rounded-2xl font-bold transition shadow-xl shadow-orange-200 flex items-center justify-center gap-3">
                <i class="bi bi-cash-coin"></i>
                Test Recharge 10k
            </button>
        </div>
    </div>

    <!-- Financial Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 text-left" id="financialStats">
        <!-- Stats will be loaded here -->
    </div>

    <!-- Two Column Layout -->
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Hospitals Section -->
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 text-left">
            <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                Abonnements Hôpitaux (SaaS)
            </h3>
            <div class="space-y-4" id="hospitalsFinancialList">
                <!-- Hospitals financial data will be loaded here -->
            </div>
        </div>

        <!-- Specialists Section -->
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 text-left">
            <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <span class="w-2 h-8 bg-purple-600 rounded-full"></span>
                Portefeuilles Spécialistes (Yango)
            </h3>
            <div class="space-y-4" id="specialistsFinancialList">
                <!-- Specialists financial data will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 mt-8 text-left">
        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
            <span class="w-2 h-8 bg-green-600 rounded-full"></span>
            Transactions Récentes
        </h3>
        <div class="space-y-4" id="recentTransactionsList">
            <!-- Recent transactions will be loaded here -->
        </div>
    </div>
</div>
