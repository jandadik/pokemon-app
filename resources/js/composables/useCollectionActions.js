import { ref, getCurrentInstance } from 'vue';
import { router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/stores/notificationStore';

export function useCollectionActions() {
    // Loading states
    const isTogglingVisibility = ref(false);
    const isSettingDefault = ref(false);
    const isDeleting = ref(false);
    const confirmDelete = ref(false);
    
    // Stores and utilities
    const notificationStore = useNotificationStore();
    const instance = getCurrentInstance();
    const $t = instance?.appContext.config.globalProperties.$t;

    // Collection actions
    const setAsDefault = (collection) => {
        if (!collection) return;
        
        isSettingDefault.value = true;
        // TODO: Add retry mechanism for network failures (5xx errors, timeouts)
        // TODO: Implement exponential backoff with max 3 retries
        router.patch(route('collections.toggle_default', collection.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                isSettingDefault.value = false;
                notificationStore.success($t('ui.notifications.success.collection_default_set'));
            },
            onError: (errors) => {
                isSettingDefault.value = false;
                console.error('Chyba při nastavování kolekce jako výchozí:', errors);
                // TODO: Distinguish between retryable (5xx, timeout) and non-retryable (4xx) errors
                notificationStore.error($t('ui.notifications.error.collection_default_failed'));
            }
        });
    };

    const toggleVisibility = (collection) => {
        if (!collection) return;
        
        isTogglingVisibility.value = true;
        router.patch(route('collections.toggle_visibility', collection.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                isTogglingVisibility.value = false;
                notificationStore.success($t('ui.notifications.success.collection_visibility_changed'));
            },
            onError: (errors) => {
                isTogglingVisibility.value = false;
                console.error('Chyba při přepínání viditelnosti kolekce:', errors);
                notificationStore.error($t('ui.notifications.error.collection_visibility_failed'));
            }
        });
    };

    const deleteCollection = (collection) => {
        if (!collection) return;
        
        isDeleting.value = true;
        router.delete(route('collections.destroy', collection.id), {
            onSuccess: () => {
                notificationStore.success($t('ui.notifications.success.collection_deleted'));
                router.visit(route('collections.index'));
            },
            onError: (errors) => {
                isDeleting.value = false;
                confirmDelete.value = false;
                console.error('Chyba při mazání kolekce:', errors);
                notificationStore.error($t('ui.notifications.error.collection_delete_failed'));
            }
        });
    };

    // Card actions
    const handleCardEdit = (item, collection) => {
        router.visit(route('collections.items.edit', { 
            collection: collection.id, 
            item: item.id 
        }));
    };

    const handleCardDelete = (item, collection) => {
        if (confirm($t('ui.messages.confirm_delete'))) {
            router.delete(route('collections.items.destroy', { 
                collection: collection.id, 
                item: item.id 
            }), {
                preserveScroll: true,
                onSuccess: () => {
                    notificationStore.success($t('ui.notifications.success.item_deleted'));
                },
                onError: (errors) => {
                    console.error('Chyba při mazání položky:', errors);
                    notificationStore.error($t('ui.notifications.error.item_delete_failed'));
                }
            });
        }
    };

    const handleCardDuplicate = (item, collection) => {
        router.post(route('collections.items.bulk_duplicate', { 
            collection: collection.id
        }), {
            item_ids: [item.id]
        }, {
            preserveScroll: true,
            onSuccess: () => {
                notificationStore.success($t('ui.notifications.success.item_duplicated'));
            },
            onError: (errors) => {
                console.error('Chyba při duplikaci položky:', errors);
                notificationStore.error($t('ui.notifications.error.item_duplicate_failed'));
            }
        });
    };

    // Bulk actions
    const handleBulkDuplicate = (selectedItems, collection, clearSelection) => {
        if (!selectedItems || selectedItems.length === 0) {
            notificationStore.warning($t('ui.notifications.error.items_selection_required'));
            return;
        }
        
        try {
            const itemIds = selectedItems.map(id => Number(id));
            
            router.post(route('collections.items.bulk_duplicate', { 
                collection: collection.id
            }), {
                item_ids: itemIds
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    clearSelection();
                    notificationStore.success($t('ui.notifications.success.items_duplicated'));
                },
                onError: (errors) => {
                    console.error('Chyba při duplikaci:', errors);
                    notificationStore.error($t('ui.notifications.error.items_duplicate_failed'));
                }
            });
        } catch (error) {
            console.error('Error during bulk duplicate:', error);
            notificationStore.error($t('ui.notifications.error.operation_failed'));
        }
    };

    const handleBulkExport = (selectedItems, collection, clearSelection) => {
        if (!selectedItems || selectedItems.length === 0) {
            notificationStore.warning($t('ui.notifications.error.items_selection_required'));
            return;
        }
        
        try {
            const itemIds = selectedItems.map(id => Number(id));
            
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = route('collections.items.export', { 
                collection: collection.id
            });
            form.target = '_blank';
            
            itemIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'item_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            const formatInput = document.createElement('input');
            formatInput.type = 'hidden';
            formatInput.name = 'format';
            formatInput.value = 'csv';
            form.appendChild(formatInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            clearSelection();
            notificationStore.success($t('ui.notifications.success.items_exported'));
        } catch (error) {
            console.error('Error during bulk export:', error);
            notificationStore.error($t('ui.notifications.error.items_export_failed'));
        }
    };

    const handleBulkEdit = (selectedItems) => {
        if (!selectedItems || selectedItems.length === 0) {
            notificationStore.warning($t('ui.notifications.error.items_selection_required'));
            return;
        }
        
        notificationStore.info(`Hromadná úprava ${selectedItems.length} položek - funkce bude implementována v další fázi`);
    };

    const handleBulkDelete = (selectedItems, collection, clearSelection) => {
        try {
            const itemIds = selectedItems.map(id => Number(id));
            
            const params = new URLSearchParams();
            itemIds.forEach(id => {
                params.append('item_ids[]', id);
            });
            
            const deleteUrl = route('collections.items.bulk_delete', { 
                collection: collection.id
            });
            
            // TODO: Implement retry mechanism for bulk operations
            // TODO: Handle partial failures - retry only failed items
            // TODO: Add progress tracking for large bulk operations
            router.delete(`${deleteUrl}?${params.toString()}`, {
                preserveScroll: true,
                onSuccess: () => {
                    clearSelection();
                    notificationStore.success($t('ui.notifications.success.items_deleted'));
                },
                onError: (errors) => {
                    console.error('Chyba při mazání:', errors);
                    // TODO: Implement smart error handling - distinguish between partial and total failures
                    notificationStore.error($t('ui.notifications.error.items_delete_failed'));
                }
            });
        } catch (error) {
            console.error('Error during bulk delete:', error);
            // TODO: Add error boundary to catch unexpected errors in bulk operations
            notificationStore.error($t('ui.notifications.error.operation_failed'));
        }
    };

    return {
        // States
        isTogglingVisibility,
        isSettingDefault,
        isDeleting,
        confirmDelete,
        
        // Collection actions
        setAsDefault,
        toggleVisibility,
        deleteCollection,
        
        // Card actions
        handleCardEdit,
        handleCardDelete,
        handleCardDuplicate,
        
        // Bulk actions
        handleBulkDuplicate,
        handleBulkExport,
        handleBulkEdit,
        handleBulkDelete,
    };
} 