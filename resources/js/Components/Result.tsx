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
        <div className="grid grid-cols-12 m-4 bg-gray-800 p-4 rounded max-h-96">
            {isEmbedded ? (
                <iframe
                    className="h-full w-full aspect-video rounded-lg col-span-5 sm:col-span-12 md:col-span-5 overflow-clip md:col-start-1 md:row-start-1"
                    src={`https://www.youtube.com/embed/${
                        props.data.video.video_id
                    }?start=${start - 1}&autoplay=${autoplay}`}
                    title="YouTube video player"
                    // frameBorder="0"
                    // allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowFullScreen
                ></iframe>
            ) : (
                <img
                    src={props.data.video.thumbnail}
                    className="rounded-t"
                    onClick={convertImgtoVid}
                />
            )}
            <div className="col-span-7 m-4 overflow-y-scroll pr-4 sm:col-span-12 md:col-span-7 sm:row-start-2 md:row-start-1 max-h-fit">
                {props.data.captions.map((value, index) => (
                    <div
                        className="flex justify-between text-gray-300 hover:text-gray-100 my-2 cursor-pointer"
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
