import { AxiosInstance } from 'axios';
import ziggyRoute, { Config as ZiggyConfig } from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    interface Batch {
        cancelled_at?: number;
        created_at: number;
        failed_jobs: number;
        finished_at?: number;
        id: string;
        name: string;
        options: [];
        pending_jobs: number;
        // processedJobs: number;
        // progress: number
        total_jobs: number;
    }
    
    interface Caption {
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
        subtitle_updated_at: string;
        created_at: number;
        updated_at: number;
    }

    var route: typeof ziggyRoute;
    var Ziggy: ZiggyConfig;
}
