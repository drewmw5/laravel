import React from 'react'

interface Props {
    captions: {
        queriedCaption: Caption;
        prevCaption?: Caption;
        nextCaption?: Caption;
    };
    setStart: Function;
    setAutoplay: Function;
    setIsEmbedded: Function;
}


export default function Captions(props: Props) {
    
    function clickTime(time: number) {
        props.setIsEmbedded(1);
        props.setStart(time)
        props.setAutoplay(1)
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

    // if (!props.captions.prevCaption || !props.captions.nextCaption) return;

    return (
        <div className="py-3 mx-8">
        <div className="text-gray-600 flex justify-between cursor-pointer mb-1" onClick={() => {
                    clickTime(props.captions.prevCaption ? props.captions.prevCaption.start : 0)
            }}>
            <div>{props.captions.prevCaption ? props.captions.prevCaption.text : ""}</div>
            <div>{toHoursAndMinutes(props.captions.prevCaption ? props.captions.prevCaption.start : 0)}</div>
        </div>
        <div className="flex justify-between text-gray-100 cursor-pointer my-1 border-y border-gray-800 text-lg" onClick={() => {
                    clickTime(props.captions.queriedCaption.start)
            }}>
                <div>{props.captions.queriedCaption.text}</div>
            {/* {highlightWord(props.captions.queriedCaption.text)} */}
            <div>{toHoursAndMinutes(props.captions.queriedCaption.start)}</div>
        </div>
        <div className="text-gray-600 flex justify-between cursor-pointer mt-1" onClick={() => {
                    clickTime(props.captions.nextCaption ? props.captions.nextCaption.start : 0)
            }}>
            <div>{props.captions.nextCaption ? props.captions.nextCaption.text : ""}</div>
            <div>{toHoursAndMinutes(props.captions.nextCaption ? props.captions.nextCaption.start : 0)}</div>
        </div>
     </div>
    )
//     function clickTime(time: number) {
//         setIsEmbedded(1);
//         setStart(time)
//         setAutoplay(1)
//     }

//     function toHoursAndMinutes(totalSeconds: number) {
//         const totalMinutes = Math.floor(totalSeconds / 60);
    
//         const seconds = totalSeconds % 60;
//         const hours = Math.floor(totalMinutes / 60);
//         const minutes = totalMinutes % 60;
    
//         if (hours === 0) {
//             let s = seconds.toString().padStart(2, "0");
//             return (
//                 <div>
//                     {minutes}
//                     {":"}
//                     {s}
//                 </div>
//             );
//         } else {
//             let m = minutes.toString().padStart(2, "0");
//             let s = seconds.toString().padStart(2, "0");
//             return (
//                 <div>
//                     {hours}
//                     {":"}
//                     {m}
//                     {":"}
//                     {s}
//                 </div>
//             );
//         }
//     }

//   return (
//     <div className="py-3 mx-8">
//                     <div className="text-gray-500 flex justify-between cursor-pointer mb-1" onClick={() => {
//                                 clickTime(captions.prevCaption.start)
//                         }}>
//                         <div>{captions.prevCaption.text}</div>
//                         <div>{toHoursAndMinutes(captions.prevCaption.start)}</div>
//                     </div>
//                     <div className="flex justify-between text-white cursor-pointer my-1 border-y border-gray-800 text-lg" onClick={() => {
//                                 clickTime(captions.queriedCaption.start)
//                         }}>
//                         {/* <div>{captions.queriedCaption.text.replace(`${props.query}`, (<span className="text-red-500">{`${props.query}`}</span>))}</div> */}
//                         <div>{toHoursAndMinutes(captions.queriedCaption.start)}</div>
//                     </div>
//                     <div className="text-gray-500 flex justify-between cursor-pointer mt-1" onClick={() => {
//                                 clickTime(captions.nextCaption.start)
//                         }}>
//                         <div>{captions.nextCaption.text}</div>
//                         <div>{toHoursAndMinutes(captions.nextCaption.start)}</div>
//                     </div>
//                  </div>
//   )
}
