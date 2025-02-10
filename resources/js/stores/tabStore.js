import { defineStore } from 'pinia'

export const useTabStore = defineStore('tab', {
    state: () => ({
        activeTab: null,
        menuItems: [
            {
                title: 'Katalog',
                text: "Katalog serii",
            },
            {
                title: 'Sbirka',
                text: "Sbirka serii nebo karet",
            }, 
            {
                title: 'Seznamy',
                text: "Uzivatelske seznamy karet",
            },
            { 
                title: 'Karty',
                text: "Vsechny jednotlive karty v katalogu",
            }
        ]
    }),
    actions: {
        setActiveTab(tab) {
            this.activeTab = tab
        }
    }
})