export interface ShoppingListItem {
    id: number | string; // number for server, string (UUID) for local
    user_id?: number | null;
    name: string;
    quantity: number;
    categories?: string[];
    in_basket: boolean;
    created_at: string; // ISO timestamp
    updated_at: string; // ISO timestamp
    synced_at?: string | null; // ISO timestamp
}

export interface ShoppingHistoryItem {
    id: number;
    user_id: number;
    name: string;
    quantity: number;
    categories?: string[];
    purchased_at: string; // ISO timestamp
    created_at: string; // ISO timestamp
    updated_at: string; // ISO timestamp
}

