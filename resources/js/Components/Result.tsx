import { useEffect, useState } from "react";
import Captions from "./Captions";

interface Props {
    data: CaptionResponse;
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

    if (!props.data.video) return <div></div>;

    return (
        // <></>
        <div
            key={props.data.video.video_id}
            className="mx-24 bg-gray-800 my-4 rounded"
        >
            {isEmbedded ? (
                <iframe
                    className="w-full aspect-video rounded"
                    src={`https://www.youtube.com/embed/${
                        props.data.video.video_id
                    }?start=${start - 1}&autoplay=${autoplay}`}
                    title="YouTube video player"
                    // frameBorder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowFullScreen
                ></iframe>
            ) : (
                <img
                        src={props.data.video.thumbnail}
                        className="rounded-t"
                    onClick={convertImgtoVid}
                />
            )}

            <div className="py-4 text-white text-xl mx-4  border-b border-gray-500">
                {props.data.video.video_title}
            </div>

            <div className="max-h-96 overflow-y-scroll">
                {props.data.captions.map((captions, index: number) => (
                    <Captions
                        captions={captions}
                        setStart={setStart}
                        setAutoplay={setAutoplay}
                        setIsEmbedded={setIsEmbedded}
                    />
                ))}
            </div>
        </div>
    );
}
