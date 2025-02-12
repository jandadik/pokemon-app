import { defineStore } from 'pinia'

export const useTabStore = defineStore('tab', {
    state: () => ({
        activeTab: 'katalog',  // Výchozí hodnota
        tabs: [
            {
                title: 'Katalog',
                value: 'katalog',
                route: 'sets.index'
            },
            {
                title: 'Sbírka',
                value: 'sbirka',
                route: 'dashboard'
            }
        ]
    }),

    actions: {
        setActiveTab(value) {
            this.activeTab = value
        }
    }
})