import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserParametersStore = defineStore('userParameters', {
    state: () => ({
        parameters: {}
    }),
    getters: {
        homepage: (state) => state.parameters.homepage || 'pokemoni',
        notifications: (state) => state.parameters.notifications ?? true,
        
        getParameter: (state) => (key, defaultValue = null) => {
            return state.parameters[key] ?? defaultValue;
        }
    },
    actions: {
        async fetchParameters() {
            try {
                const response = await axios.get('/user/parameters');
                // Ujistíme se, že máme čistý objekt
                this.parameters = response.data || {};
            } catch (error) {
                console.error('Error fetching user parameters:', error);
                this.parameters = {};
            }
        },
        async updateParameters(newParameters) {
            try {
                const response = await axios.put('/user/parameters', newParameters);
                this.parameters = response.data || {};
            } catch (error) {
                console.error('Error updating user parameters:', error);
            }
        },
        async saveParameters() {
            try {
                const response = await axios.post('/user/parameters', this.parameters);
                this.parameters = response.data || {};
            } catch (error) {
                console.error('Error saving user parameters:', error);
            }
        },
    }
});
