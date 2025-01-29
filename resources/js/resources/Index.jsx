import React from "react";
import ReactDOM from "react-dom/client";
import Cookies from "js-cookie";
import { BrowserRouter, Route, Routes, Navigate } from "react-router-dom";
import {
    Home,
    Login,
    Sekolah,
    Kelas,
    ChangePassword,
    ForgetPassword,
    Mapel,
    UpdateDataGuru,
    GetById,
    Siswa,
    Guru,
    Profile,
    Register,
    KelasById,
} from "./src/pages/Index";

export default function Index() {
    return (
        <BrowserRouter>
            <Routes>
                {/* Start UnAuthorization */}
                {/* Login */}
                <Route
                    path="/login"
                    element={
                        <UnAthenticated>
                            <Login />
                        </UnAthenticated>
                    }
                />
                {/* Register */}
                <Route
                    path="/register"
                    element={
                        <UnAthenticated>
                            <Register />
                        </UnAthenticated>
                    }
                />
                {/* Forget password */}
                <Route
                    path="/forget-password"
                    element={
                        <UnAthenticated>
                            <ForgetPassword />
                        </UnAthenticated>
                    }
                />

                {/* End UnAuthorization */}

                {/* Start Authorization */}
                {/* Main root */}
                <Route
                    path="/"
                    element={
                        <PrivateRoute>
                            <Home />
                        </PrivateRoute>
                    }
                />
                {/* Profile */}
                <Route
                    path="/profile"
                    element={
                        <PrivateRoute>
                            <Profile />
                        </PrivateRoute>
                    }
                />
                {/* Change password */}
                <Route
                    path="/change-password"
                    element={
                        <PrivateRoute>
                            <ChangePassword />
                        </PrivateRoute>
                    }
                />
                {/* Guru */}
                <Route
                    path="/guru"
                    element={
                        <PrivateRoute>
                            <Guru />
                        </PrivateRoute>
                    }
                />
                {/* Update guru by id */}
                <Route
                    path="/guru/:nip"
                    element={
                        <PrivateRoute>
                            <UpdateDataGuru />
                        </PrivateRoute>
                    }
                />
                {/* Siswa */}
                <Route
                    path="/siswa"
                    element={
                        <PrivateRoute>
                            <Siswa />
                        </PrivateRoute>
                    }
                />
                {/* Update data siswa */}
                <Route
                    path="/siswa/:nis"
                    element={
                        <PrivateRoute>
                            <GetById />
                        </PrivateRoute>
                    }
                />
                {/* Mapel */}
                <Route
                    path="/mapel"
                    element={
                        <PrivateRoute>
                            <Mapel />
                        </PrivateRoute>
                    }
                />
                {/* Kelas */}
                <Route
                    path="/kelas"
                    element={
                        <PrivateRoute>
                            <Kelas />
                        </PrivateRoute>
                    }
                />

                {/* Kelas by id */}
                <Route
                    path="/kelas/:nip/:id"
                    element={
                        <PrivateRoute>
                            <KelasById />
                        </PrivateRoute>
                    }
                />

                {/* Profile Sekolah */}
                <Route
                    path="/profile-sekolah"
                    element={
                        <PrivateRoute>
                            <Sekolah />
                        </PrivateRoute>
                    }
                />
                {/* End Authorization */}
            </Routes>
        </BrowserRouter>
    );
}

function PrivateRoute({ children }) {
    const cookieAuth = Cookies.get("authentication");

    if (cookieAuth) {
        return children;
    }
    return <Navigate to="/login" replace={true} />;
}

function UnAthenticated({ children }) {
    const cookieAuth = Cookies.get("authentication");
    if (!cookieAuth) {
        return children;
    }
    return <Navigate to="/" replace={true} />;
}

if (document.getElementById("root")) {
    ReactDOM.createRoot(document.getElementById("root")).render(<Index />);
}
