import { Batch, Video as VideoType } from "@/Types/types";
import React from "react";
import SecondaryButton from "@/Components/SecondaryButton";

type Video = VideoType;

interface Props {
    video: Video & {
        job_batches: Batch[];
    };
    updateVideo(videoId: string): void;
}

export default function Video(props: Props) {
    
    function returnDate(time: number) {
        const date = new Date(time * 1000);

        return date.toLocaleDateString() + " "
            + (date.getHours() < 10 ? "0" + date.getHours() : date.getHours())
            + ":" + (date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes()); 
    }

    return (
        <details className="text-gray-100">
            <summary className="flex">
                <img src={props.video.thumbnail} className="h-16" loading="lazy"></img>
                <h3>{props.video.video_title}</h3>
            </summary>
            <SecondaryButton onClick={(e) => {
                    props.updateVideo(props.video.video_id)
                }}>
                    Update Video
                </SecondaryButton>
            <div className="grid grid-cols-6">
                {props.video.job_batches.map((value, index) => (

                        <React.Fragment key={index}>
                        <div>{value.name}</div>
                        {/* <div> */}
                            <div>{value.total_jobs}</div>
                            <div>{value.pending_jobs}</div>
                        {/* </div> */}
                        <div>{returnDate(value.created_at)}</div>
                        <div>{value.finished_at ? returnDate(value.finished_at) : <></>}</div>
                        <div>{value.cancelled_at ? returnDate(value.cancelled_at) : <></>}</div>
                        {/* </div> */}
                        </React.Fragment>
                ))}
            </div>
        </details>
    );
}
