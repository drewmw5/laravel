
import Result from "@/Components/Result";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import axios from "axios";
import { Component } from "react";
import Video from "@/Components/Video";
import { PageProps } from "@/types";

interface Props extends PageProps {

    count: number;
    totalVideos: number;
    videos: Array<Video & {
        job_batches: Batch[];
    }>;
    // videos?: Array<Video>;
}

interface State {
    search: string;
    resultCount: number;
    resultCountLimit: number;
    videoIdUpdate: string;
    results?: Array<Caption[]>;
    jobs?: Array<Batch[]>;
}

export default class Dashboard extends Component<Props> {
    state: State;
    constructor(props: Props) {
        super(props);
        this.state = {
            resultCount: 1,
            resultCountLimit: 5,
            search: "",
            videoIdUpdate: "",
        };
    }

    componentDidMount(): void {
        this.echo();
        console.log(this.props)
    }

    echo = () => {
        const echo = window.Echo;
        echo.channel("caption").listen("CaptionUpdate", (e: any) => {

        });
        echo.channel("video").listen("VideoUpdate", (e: any) => {

        });
    };

    addPlaylist = () => {
        const data = {
            playlistId: this.state.search,
        };

        axios
            .post("/playlist", {
                data: data,
            })
            .then((res) => {
                // let VIDEOS: any[string] = [];
                // Object.keys(res.data).forEach((value: string, index) => {
                //     VIDEOS[res.data[value].id] = res.data[value];
                // });
                // const STATE = this.state;
                // STATE.jobs = VIDEOS;
                // this.setState(STATE);
            });
    };

    searchForCaptions(value: string) {
        const data = {
            search: value,
        };
        if (data.search.length === 0) {
            const STATE = this.state;
            STATE.results = [[]];
            this.setState(STATE);
        }

        if (data.search.length >= 3) {
            axios
                .get("/captions", {
                    params: data,
                })
                .then((res) => {
                    console.log(res);
                    const STATE = this.state;
                    STATE.results = res.data;
                    this.setState(STATE);
                    // this.getVideoData();
                });
        }
    }

    // getVideoData() {
    //     const STATE = this.state;
    //     let data: string[] = [];
    //     if (!STATE.results) {
    //         return;
    //     } else {
    //         Object.keys(STATE.results).map((value, index) => {
    //             data.push(value);
    //         });
    //     }
    //     console.log(STATE.results);

    //     axios.get(`/api/videos/`, { params: data }).then((res) => {
    //         const STATE = this.state;
    //         console.log(res.data);
    //         res.data.forEach((element) => {
    //             if (!STATE.results) return;
    //             STATE.results[element.videoId].options = [];
    //             STATE.results[element.videoId].options = element;
    //         });
    //         this.setState(STATE);
    //         console.log(this.state);
    //     });
    // }

    getJobs() {
        axios.get("/jobs").then((res) => {
            const STATE = this.state;
            STATE.jobs = res.data;
            this.setState(STATE);
        });
    }

    updateVideoId(id?: string) {
        const videoId = id || this.state.videoIdUpdate;
        console.log(videoId)
        axios.post('/video', {
            videoId: videoId,
        }).then((res) => {

        })
    }

    render() {

        const STATE = this.state;

        let typingTimer: NodeJS.Timeout | undefined;

        return (
            <AuthenticatedLayout
                auth={this.props.auth}
                error={this.props.errors}
                header={
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Dashboard
                    </h2>
                }
            >
                <Head title="Dashboard" />

                <div className="py-12">
                    <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div className="w-full mb-5 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg pr-7">
                            <input
                                type="text"
                                className="w-full m-3 text-gray-200 bg-gray-900 rounded"
                                placeholder="Search..."
                                onKeyDown={() => {
                                    clearTimeout(typingTimer);
                                }}
                                onKeyUp={(event) => {
                                    clearTimeout(typingTimer);
                                    typingTimer = setTimeout(() => {
                                        STATE.resultCount = 1;
                                        STATE.resultCountLimit = 5;
                                        this.setState(STATE);
                                        this.searchForCaptions(event.currentTarget.value);
                                    }, 1000);
                                }}
                            ></input>
                            <div className="text-white">
                                <input
                                    type="text"
                                    className="w-auto m-3 text-gray-200 bg-gray-900 rounded"
                                    placeholder="Add playlist"
                                    onKeyUp={(e) => {
                                        this.setState({
                                            search: e.currentTarget.value,
                                        });
                                    }}
                                ></input>
                                <button
                                    className="px-4 py-2 ml-4 border rounded"
                                    onClick={() => this.addPlaylist()}
                                >
                                    Click to add Playlist
                                </button>
                                <button
                                    className="px-4 py-2 ml-4 border rounded"
                                    onClick={() => this.getJobs()}
                                >
                                    Get Jobs
                                </button>
                                <input
                                    type="text"
                                    className="w-auto m-3 text-gray-200 bg-gray-900 rounded"
                                    placeholder="Video Id to update"
                                    onKeyUp={(e) => {
                                        this.setState({
                                            videoIdUpdate: e.currentTarget.value,
                                        });
                                    }}
                                ></input>
                                <button
                                    className="px-4 py-2 ml-4 border rounded"
                                    onClick={(e) => {
                                    this.updateVideoId();
                                }}>
                                    Update Video
                                </button>
                                {this.props.count + "/" + this.props.totalVideos}
                            </div>
                        </div>
                    </div>
                    <div>
                        {
                            this.props.videos.map((value, index) => (
                                // console.log(value)
                                <Video video={value} updateVideo={this.updateVideoId.bind(this)} key={index}/>
                            ))
                        }
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }
}
