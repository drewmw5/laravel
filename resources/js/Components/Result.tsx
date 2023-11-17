import { useEffect, useState } from "react";

interface VideoId {
    video_id: string;
}

interface Caption {
    text: string;
    start: number;
    duration: number;
}

interface Props {
    data?: {
        video: Video;
        captions: Caption[];
    };
}

export default function Result(props: Props) {
    const [start, setStart] = useState(0);
    const [autoplay, setAutoplay] = useState(0);
    const [isEmbedded, setIsEmbedded] = useState(0);

    useEffect(() => {
        setIsEmbedded(0);
        setStart(0);
        setAutoplay(0);
    }, [props.data]);

    function convertImgtoVid() {
        setIsEmbedded(1);
        setStart(1);
        setAutoplay(1);
    }

    function clickTime(time: number) {
        setIsEmbedded(1);
        setStart(time);
        setAutoplay(1);
    }

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

    console.log(props);
    if (props.data == undefined) return;

    return (
        <div className="grid grid-cols-12 grid-rows-2 p-4 m-4 bg-gray-800 rounded max-h-96">
            {isEmbedded ? (
                <div className="w-full h-full col-span-5 rounded-lg aspect-video sm:col-span-12 md:col-span-5 overflow-clip md:col-start-1 md:row-start-1">
                <iframe
                    src={`https://www.youtube.com/embed/${
                        props.data.video.video_id
                    }?start=${start - 1}&autoplay=${autoplay}`}
                    title="YouTube video player"
                    // frameBorder="0"
                    // allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowFullScreen
                    ></iframe>
                </div>
            ) : (
                <img
                    src={props.data.video.thumbnail}
                    className="rounded-t"
                    onClick={convertImgtoVid}
                />
            )}
            <div className="w-full h-full col-span-5 rounded-lg aspect-video sm:col-span-12 md:col-span-5 overflow-clip md:col-start-1 md:row-start-1">
                {props.data.captions.map((value, index) => (
                    <div
                        className="flex justify-between my-2 text-gray-300 cursor-pointer hover:text-gray-100"
                        key={index}
                        onClick={(e) => {
                            clickTime(value.start - 2);
                        }}
                    >
                        <div>{value.text}</div>
                        <div>{toHoursAndMinutes(value.start)}</div>
                    </div>
                ))}
            </div>
        </div>
    );
}
