import { useEffect, useState } from "react";
import { Token } from "../../utils/utils";
import { Link, useNavigate } from "react-router-dom";

export default function FetchAspiration() {
    const [asp, setAsp] = useState([]);
    const [filterTarget, setFilterTarget] = useState("")
    const navigate = useNavigate()

    async function fetchAspiration() {
        const res = await fetch("http://localhost:8000/api/aspirations", {
            headers: {
                "Authorization": `Bearer ${Token}`,
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            method: "GET",
        });
        const data = await res.json();
        setAsp(Object.values(data.Aspiration));
        console.log(Object.values(data.Aspiration));
    }

    async function handleLogout(e) {
        e.preventDefault();
        const res = await fetch("http://localhost:8000/api/logout", {
            headers: {
                "Authorization": `Bearer ${Token}`,
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            method: "POST",
        });
        const data = await res.json()

        if(res.status == 200){
            navigate("/")
        }

        console.log(data)
    }

    useEffect(() => {
        fetchAspiration();
    }, []);

    const filteredAsp = asp.filter(item => {
        return filterTarget === "" || item.to === filterTarget;
    }).slice().sort((a, b) => b.date.localeCompare(a.date));

    return (
        <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4">
            <div className="flex justify-evenly items-center">
                <h1 className="text-2xl font-bold text-red-700 mb-6 text-center">
                    📜 Daftar Aspirasi
                </h1>
                <select onChange={e => setFilterTarget(e.target.value)} className="mb-6 bg-red-100 rounded-xs">
                    <option value="">ALL</option>
                    <option value="MPK">MPK</option>
                    <option value="OSIS">OSIS</option>
                    <option value="SEKOLAH">SEKOLAH</option>
                </select>
            </div>

            <div className="flex flex-col gap-4 max-w-2xl mx-auto">
                {

                    filteredAsp.map((item, i) => (
                        <Link to={`/aspirations/${item.id}`}
                            key={i}
                            className={`bg-white rounded-xl shadow-md p-5 border-l-8 hover:shadow-lg transition-shadow ${item.to == "MPK" ? "border-red-600" : item.to== "OSIS"?"border-blue-500" : "border-gray-400"} `}
                        >
                            <h3 className={`text-lg font-semibold ${item.to == "MPK" ? 'text-red-700' : item.to == "OSIS" ? "text-blue-700" : "text-black"}`}>
                                🎯 {item.to}
                            </h3>
                            <p className="text-gray-700 mt-1">{item.message}</p>
                            {item.date && (
                                <p className="text-xs text-gray-500 mt-3">
                                    {new Date(item.date).toLocaleString("id-ID", {
                                        dateStyle: "long",
                                        timeStyle: "short"
                                    })}
                                </p>
                            )}
                        </Link>
                    ))}
            </div>
            <div className="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
                    <button onClick={e=>handleLogout(e)}>Logout</button>
            </div>
        </div>
    );
}
