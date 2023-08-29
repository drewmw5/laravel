export interface Batch {
    cancelledAt?: string;
    createdAt: string;
    failedJobs: number;
    finishedAt?: string;
    id: string;
    name: string;
    options: [];
    pendingJobs: number;
    processedJobs: number;
    progress: number
    totalJobs: number;
}

export interface Caption {
    id: number;
    video_id: string;
    text: string;
    start: number;
    duration: number;
}

export interface Video {
    video_id: string;
    video_title: string;
    description: string;
    video_owner_channel_title: string;
    published_at: string;
    thumbnail: string;
    total_jobs: number;
    subtitle_updated_at: string;
    created_at: string;
    updated_at: string;
}