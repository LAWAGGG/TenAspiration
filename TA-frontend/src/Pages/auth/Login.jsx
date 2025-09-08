import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { setToken } from "../../utils/utils";

export default function Login() {
    const [name, setName] = useState("");
    const [pass, setPass] = useState("");
    const navigate = useNavigate();

    async function handleLogin(e) {
        e.preventDefault();
        const res = await fetch("http://localhost:8000/api/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                "name": name,
                "password": pass,
            })
        });
        const data = await res.json();

        if (res.status === 200) {
            setToken(data.token);
            navigate("/aspirations");
        } else {
            alert("Input incorrect");
            window.location.reload();
        }
    }

    return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 via-white to-red-100 p-4">
            <div className="bg-white shadow-lg rounded-xl p-8 w-full max-w-md border-t-4 border-red-500">
                <h1 className="text-2xl font-bold text-red-600 mb-6 text-center">🔑 Login</h1>
                <form onSubmit={handleLogin} className="space-y-4">
                    <input
                        type="text"
                        onChange={e => setName(e.target.value)}
                        placeholder="Name"
                        className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"
                    />
                    <input
                        type="password"
                        onChange={e => setPass(e.target.value)}
                        placeholder="Password"
                        className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"
                    />
                    <button
                        type="submit"
                        className="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition"
                    >
                        Login
                    </button>
                </form>
                <p className="text-xs text-gray-500 mt-4 text-center">
                    © {new Date().getFullYear()} Your App. All rights reserved.
                </p>
            </div>
        </div>
    );
}
