import { Batch, Caption, Video } from "@/Types/types";

import Result from "@/Components/Result";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import axios from "axios";
import { Component } from "react";

interface Props {
    auth: any;
    errors: any;
}

interface State {
    search: string;
    resultCount: number;
    resultCountLimit: number;
    videos?: Array<Video>;
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
        };
    }

    componentDidMount(): void {
        this.echo();
        this.getJobs();
        // setInterval(() => {
        //     this.getJobs();
        // }, 3000)
    }

    echo = () => {
        const echo = window.Echo;
        echo.channel("caption").listen("CaptionUpdate", (e: any) => {
            // const STATE = this.state;
            // if (!STATE.videos) return;
            // STATE.videos[e.videoId].options.jobCount = e.index + 1;
            // this.setState(STATE);
        });
        echo.channel("video").listen("VideoUpdate", (e: any) => {
            // console.log(e.videoId);
        });
    };

    addPlaylist = () => {
        const data = {
            playlistId: this.state.search,
        };

        axios
            .post("/api/captions", {
                data: data,
            })
            .then((res) => {
                let VIDEOS: any[string] = [];
                Object.keys(res.data).forEach((value: string, index) => {
                    VIDEOS[res.data[value].id] = res.data[value];
                });
                const STATE = this.state;
                STATE.jobs = VIDEOS;
                this.setState(STATE);
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
                .get("/api/captions", {
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

    getVideoData() {
        const STATE = this.state;
        let data: string[] = [];
        if (!STATE.results) {
            return;
        } else {
            Object.keys(STATE.results).map((value, index) => {
                data.push(value);
            });
        }
        console.log(STATE.results);

        axios.get(`/api/videos/`, { params: data }).then((res) => {
            const STATE = this.state;
            console.log(res.data);
            res.data.forEach((element) => {
                if (!STATE.results) return;
                STATE.results[element.videoId].options = [];
                STATE.results[element.videoId].options = element;
            });
            this.setState(STATE);
            console.log(this.state);
        });
    }

    getJobs() {
        axios.get("/jobs").then((res) => {
            const STATE = this.state;
            STATE.jobs = res.data;
            this.setState(STATE);
        });
    }

    render() {
        const STATE = this.state;
        console.log(STATE);
        const videos: any[] = [];

        if (STATE.videos) {
            Object.keys(STATE.videos).forEach(function (value: string, index) {
                if (!STATE.videos) return;
                videos.push(STATE.videos[value]);
            });
        }

        var typingTimer: NodeJS.Timeout | undefined;

        return (
            <AuthenticatedLayout
                auth={this.props.auth}
                error={this.props.errors}
                header={
                    <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Dashboard
                    </h2>
                }
            >
                <Head title="Dashboard" />

                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg w-full pr-7 mb-5">
                            <input
                                type="text"
                                className="m-3 rounded bg-gray-900 w-full text-gray-200"
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
                                        this.searchForCaptions(event.target.value);
                                    }, 1000);
                                }}
                            ></input>
                            <div className="text-white">
                                <input
                                    type="text"
                                    className="m-3 rounded bg-gray-900 w-auto text-gray-200"
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
                            </div>
                        </div>
                    </div>
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 text-white">
                        {!STATE.results ? (
                            <></>
                        ) : (
                            Object.keys(STATE.results).map((videoId, index) => {
                                if (!STATE.results) {
                                    return;
                                } else {
                                    STATE.resultCount = index;
                                    if (STATE.resultCount >= STATE.resultCountLimit) return;
                                    console.log(STATE);
                                    return (
                                        <Result
                                            key={videoId}
                                            data={STATE.results[videoId]}
                                        />
                                    );
                                }
                            })
                        )}
                        { (STATE.resultCount < STATE.resultCountLimit) ? <></> :
                            <div
                                className=""
                                onClick={() => {
                                    STATE.resultCountLimit += 3;
                                    this.setState(STATE);
                                    console.log(this.state);
                                }}
                            >Load more...</div>}
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }
}
