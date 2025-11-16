import { ref, watch, type Ref } from 'vue';

/**
 * Composable for managing LocalStorage with reactive Vue refs
 * @param key - LocalStorage key
 * @param defaultValue - Default value if key doesn't exist
 * @returns Reactive ref that syncs with LocalStorage
 */
export function useLocalStorage<T>(key: string, defaultValue: T): Ref<T> {
    const stored = localStorage.getItem(key);
    const data = ref<T>(
        stored ? JSON.parse(stored) : defaultValue
    ) as Ref<T>;

    watch(
        data,
        (newValue) => {
            try {
                localStorage.setItem(key, JSON.stringify(newValue));
            } catch (error) {
                console.error(`Failed to save to localStorage key "${key}":`, error);
            }
        },
        { deep: true }
    );

    return data;
}

/**
 * Get item from LocalStorage without reactivity
 */
export function getLocalStorageItem<T>(key: string, defaultValue: T): T {
    const stored = localStorage.getItem(key);
    return stored ? JSON.parse(stored) : defaultValue;
}

/**
 * Set item in LocalStorage without reactivity
 */
export function setLocalStorageItem<T>(key: string, value: T): void {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
        console.error(`Failed to save to localStorage key "${key}":`, error);
    }
}

/**
 * Remove item from LocalStorage
 */
export function removeLocalStorageItem(key: string): void {
    localStorage.removeItem(key);
}

