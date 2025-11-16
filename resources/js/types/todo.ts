export interface TodoItem {
    id: number | string; // number for server, string (UUID) for local
    user_id?: number | null;
    title: string;
    description?: string | null;
    tags?: string[];
    due_date?: string | null; // ISO date string
    created_at: string; // ISO timestamp
    updated_at: string; // ISO timestamp
    synced_at?: string | null; // ISO timestamp
}

