import { useEffect, useState } from "react"
import { Link, useParams } from "react-router-dom"
import { getToken } from "../../utils/utils"

export default function AspirationDetail() {
    const [asp, setAsp] = useState({})
    const [message, setMessage] = useState("")
    const [to, setTo] = useState("")
    const [date, setDate] = useState("")
    const params = useParams()
    const dates = new Date(date).toLocaleString("id-ID", {
        dateStyle: "long",
        timeStyle: "short"
    })  

    async function fetchAspiration() {
        const res = await fetch(`http://localhost:8000/api/aspirations/${params.id}`, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${getToken()}`,
            },
            method: "GET",
        });
        const data = await res.json();
        setAsp(Object.values(data.Aspiration));
        setMessage(data.Aspiration.message)
        setTo(data.Aspiration.to)
        setDate(data.Aspiration.created_at)
        console.log(data.Aspiration);
    }

    function handleCopy() {
        const format = `Message: ${message}\nTo: ${to}\nDate: ${dates}`
        navigator.clipboard.writeText(format)
    }

    useEffect(() => {
        fetchAspiration()
    }, [])
    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 flex items-center justify-center p-6">
            <div className="bg-white shadow-lg rounded-xl p-6 max-w-md w-full border-l-8 border-red-500">
                <h1 className="text-2xl font-bold text-red-700 mb-4">📌 Detail Aspirasi</h1>

                <div className="space-y-3">
                    <p className="text-gray-700">
                        <span className="font-semibold text-red-600">Message:</span> {message}
                    </p>
                    <p className="text-gray-700">
                        <span className="font-semibold text-red-600">To:</span> {to}
                    </p>
                    <p className="text-xs text-gray-500 mt-3">
                        {new Date(date).toLocaleString("id-ID", {
                            dateStyle: "long",
                            timeStyle: "short"
                        })}
                    </p>
                    <div className="flex justify-end">
                        <button className=" bg-gray-200 px-3 py-1 rounded-xl" onClick={handleCopy}>copy</button>
                    </div>
                </div>
            </div>
            <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
                <Link className="" to="/aspirations">Go back </Link>
            </div>
        </div>
    );

}