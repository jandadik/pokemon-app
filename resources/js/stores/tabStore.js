import { defineStore } from 'pinia'
//import { useUserParametersStore } from '@/stores/userParametersStore'
// const userParametersStore = useUserParametersStore();

export const useTabStore = defineStore('tab', {
    state: () => ({
        activeTab: null,  // Změníme na null pro inicializaci
        tabs: [
            {
                title: 'Home',
                value: 'dashboard',
                route: 'dashboard'
            },
            {
                title: 'Katalog',
                value: 'sets',
                route: 'sets.index'
            },
            {
                title: 'Admin',
                value: 'admin',
                route: 'admin.index'
            }
        ]
    }),

    getters: {
        // Přidáme getter pro získání route podle value
        getRouteByValue: (state) => (value) => {
            const tab = state.tabs.find(tab => tab.value === value);
            return tab ? tab.route : 'dashboard';
        }
    },

    actions: {
        setActiveTab(value) {
            this.activeTab = value;
        },

        // async initializeFromParameters() {
        //     const userParametersStore = useUserParametersStore();
            
        //     // Počkáme na načtení parametrů, pokud ještě nejsou načteny
        //     if (Object.keys(userParametersStore.parameters).length === 0) {
        //         await userParametersStore.fetchParameters();
        //     }

        //     // Pouze pokud ještě nebyl inicializován
        //     if (this.activeTab === null) {
        //         const homepage = userParametersStore.homepage;
        //         const tab = this.tabs.find(tab => tab.route === homepage);
        //         if (tab) {
        //             this.activeTab = tab.value;
        //         } else {
        //             // Fallback na první tab
        //             this.activeTab = this.tabs[0].value;
        //         }
        //     }
        // }
    }
})