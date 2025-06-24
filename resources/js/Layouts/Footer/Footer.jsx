import ApplicationLogo from "@/Components/ApplicationLogo";
import Section from "@/Components/Section";

import { Link } from "@inertiajs/react";
import React from "react";

import Newsletter from "./Newsletter";

const Footer = () => {
    return (
        <div>
            <Section>
                <div className="py-section container relative z-10">
                    <Newsletter />
                </div>
            </Section>
            <div className="">
                <div className="container">
                    <div>
                        <div className="flex items-center justify-between gap-3 border-b  pb-4">
                            <div className="">
                                <ApplicationLogo className="text-3xl" />
                            </div>
                            <div className="flex items-center gap-3 text-base">
                                <a
                                    href="https://www.facebook.com/people/Parque-Brinca-Este/61550657426937/"
                                    target="_blank"
                                    className="rounded-full bg-white p-2 transition duration-150 hover:bg-blue-400"
                                >
                                    <img
                                        src="/img/logo/facebook-logo.png" // Cambia esta ruta por la ruta real de la imagen del logo  
                                        alt="Facebook"
                                        className="w-6 h-6" // Ajusta el tamaño según sea necesario  
                                    />
                                </a>
                                <a
                                    href="https://www.instagram.com/brincaeste/?hl=en"
                                    target="_blank"
                                    className="rounded-full bg-white p-2 transition duration-150 hover:bg-blue-400"
                                >
                                    <img
                                        src="/img/logo/instagram-logo.svg" // Cambia esta ruta por la ruta real de la imagen del logo  
                                        alt="Instagram"
                                        className="w-6 h-6" // Ajusta el tamaño según sea necesario  
                                    />
                                </a>
                                <a
                                    href="https://www.tiktok.com/@brinca.este"
                                    target="_blank"
                                    className="rounded-full bg-white p-2 transition duration-150 hover:bg-blue-400"
                                >
                                    <img
                                        src="/img/logo/Tiktok-logo.png" // Cambia esta ruta por la ruta real de la imagen del logo  
                                        alt="TikTok"
                                        className="w-6 h-6" // Ajusta el tamaño según sea necesario  
                                    />
                                </a>
                            </div>
                        </div>
                        <div className="flex flex-col justify-between gap-3 py-5 text-sm font-medium lg:flex-row lg:items-center">
                            <div className="grow">
                                <p>
                                    Copyright © 2024.Todos los Derechos Reservados {" "}
                                    <Link
                                        href={route("privacy_policy")}
                                        className="text-blue-500"
                                    >
                                        Brinca Este 2024, C.A.
                                    </Link>
                                </p>
                            </div>

                            <Link
                                href={route("about_us")}
                                className="hover:text-blue-500"
                            >
                                Sobre nosotros
                            </Link>
                            <Link
                                href={route("terms_of_service")}
                                className="hover:text-blue-500"
                            >
                                Normas del parque
                            </Link>
                            <Link
                                href={route("privacy_policy")}
                                className="hover:text-blue-500"
                            >
                                Política de privacidad
                            </Link>
                            <Link
                                href={route("faq")}
                                className="hover:text-blue-500"
                            >
                                Faq
                            </Link>
                            <span className="mx-2" />
                            <Link
                                href={route("stand")}
                                className="hover:text-blue-500"
                            >
                                Stand de Comida
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Footer;
