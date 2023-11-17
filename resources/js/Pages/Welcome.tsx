import { Head, useForm } from "@inertiajs/react";
import { PageProps } from "@/types";
import TextInput from "@/Components/TextInput";
import { useEffect, useState } from "react";
import Result from "@/Components/Result";
import SecondaryButton from "@/Components/SecondaryButton";

export default function Welcome({
    auth,
    laravelVersion,
    phpVersion,
    results,
    query,
    page,
    pageCount,
}: PageProps<{
    laravelVersion: string;
    phpVersion: string;
    results: [];
    query: string;
    page: number;
    pageCount: number;
}>) {

    const [captions, setCaptions] = useState<Array<CaptionResponse> & { [key: string]: any }> ([]);

    const { data, setData, get, processing } = useForm("Search", {
        query: query || "",
        page: page,
    });

    useEffect(() => {
        if (!results) return;
        setCaptions(results);
        setData("query", query);

        echo();
    }, []);

    useEffect(() => {
        if (data.page == page) return;
        get("/search", {
            data: data,
        });
    }, [data.page]);

    function echo() {
        const echo = window.Echo;
        echo.channel("caption").listen("CaptionUpdate", (e: any) => {});
        echo.channel("video").listen("VideoUpdate", (e: any) => {});
    }

    function Paginator() {
        let test = [];
        for (let i = 1; i <= pageCount; i++) {
            test.push(
                <SecondaryButton
                    className="mx-1"
                    value={i}
                    onClick={(e) => {
                        let button = e.target as HTMLButtonElement;
                        setData("page", parseInt(button.value));
                    }}
                    key={i}
                >
                    {i}
                </SecondaryButton>
            );
        }
        return <div className="mx-4">{test}</div>;
    }

    return (
        <>
            <Head title="Welcome" />
            <div className="relative min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white grid grid-cols-12">
                <div className="flex flex-col mb-4 sm:col-span-12 md:col-span-10 md:col-start-2">
                    <TextInput
                        type="text"
                        value={data.query}
                        onChange={(e) => {
                            setData("query", e.target.value);
                        }}
                        onKeyDown={(e) => {
                            if (e.key === "Enter") {
                                setData('page', 1);
                                get("/search", {
                                    data: data,
                                });
                            }
                        }}
                    />
                    {/* <PrimaryButton onClick={}>Submit</PrimaryButton> */}
                    {Object.keys(captions)?.map((value, index) => (
                        <Result data={captions[value]} key={index} />
                    ))}
                    <div className="flex justify-center">
                        <SecondaryButton
                            onClick={(e) => {
                                setData("page", data.page - 1);
                            }}
                        >
                            Previous Page
                        </SecondaryButton>
                        <Paginator />
                        <SecondaryButton
                            onClick={(e) => {
                                setData("page", data.page + 1);
                            }}
                        >
                            Next Page
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </>
    );
}
