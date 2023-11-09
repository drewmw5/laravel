import { Caption, Video } from "@/Types/types";
import React, { useState } from "react";

function toHoursAndMinutes(totalSeconds: number) {
    const totalMinutes = Math.floor(totalSeconds / 60);

    const seconds = totalSeconds % 60;
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;

    if (hours === 0) {
        let s = seconds.toString().padStart(2, "0");
        return (
            <div>
                {minutes}
                {":"}
                {s}
            </div>
        );
    } else {
        let m = minutes.toString().padStart(2, "0");
        let s = seconds.toString().padStart(2, "0");
        return (
            <div>
                {hours}
                {":"}
                {m}
                {":"}
                {s}
            </div>
        );
    }
}

export default function Result(props: Props) {
    
    function clickTime(time: number) {
        setIsEmbedded(1);
        setStart(time)
        setAutoplay(1)
    }

    console.log(props.data);

    const [start, setStart] = useState(0);
    const [autoplay, setAutoplay] = useState(0);
    const [isEmbedded, setIsEmbedded] = useState(0);

    if (!props.data.video) return <div></div>;

    return (
        <div key={props.data.video.video_id} className="pb-5">
            {isEmbedded ? <iframe
                className="w-full aspect-video rounded"
                src={`https://www.youtube.com/embed/${props.data.video.video_id}?start=${start}&autoplay=${autoplay}`}
                title="YouTube video player"
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowFullScreen
            ></iframe> : <img src={ props.data.video.thumbnail} />}
            
            <div className="pt-2">{props.data.video.video_title}</div>
            {props.data.captions.map((captions, index: number) => (
                <div className="py-3">
                    <div className="text-gray-500 flex justify-between">
                        <div>{captions.prevCaption.text}</div>
                        <div className="cursor-pointer" 
                            onClick={() => {
                                clickTime(captions.prevCaption.start)
                        }}>{toHoursAndMinutes(captions.prevCaption.start)}</div>
                    </div>
                    <div className="flex justify-between">
                        <div>{captions.caption.text}</div>
                        <div className="cursor-pointer" 
                            onClick={() => {
                                clickTime(captions.caption.start)
                        }}>{toHoursAndMinutes(captions.caption.start)}</div>
                    </div>
                    <div className="text-gray-500 flex justify-between">
                        <div>{captions.nextCaption.text}</div>
                        <div className="cursor-pointer" 
                            onClick={() => {
                                clickTime(captions.nextCaption.start)
                        }}>{toHoursAndMinutes(captions.nextCaption.start)}</div>
                    </div>
                 </div>
            ))}
        </div>
    );
}

interface Props {
    data: {
        video: Video,
        captions: [{
            caption: Caption,
            prevCaption: Caption,
            nextCaption: Caption,
        }]
    }
}
