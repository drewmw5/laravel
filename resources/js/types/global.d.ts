import { AxiosInstance } from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js/types/src/core/pusher";
import ziggyRoute, { Config as ZiggyConfig } from "ziggy-js";

declare global {
    export interface Window {
        axios: AxiosInstance;
        Pusher: Pusher;
        Echo: Echo;

    }

    export interface Batch {
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
        subtitle_updated_at: string;
        created_at: number;
        updated_at: number;
    }

    interface CaptionResponse {
        captions: [{
            queriedCaption: Caption;
            prevCaption?: Caption;
            nextCaption?: Caption;
        }];
        video: Video;
    }

    var route: typeof ziggyRoute;
    var Ziggy: ZiggyConfig;
}
