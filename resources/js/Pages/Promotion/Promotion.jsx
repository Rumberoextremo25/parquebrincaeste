import React, { useState } from "react";
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from "@/Components/Hero/BannerHero";

// Datos de las promociones (externalizados para mayor claridad)
const promotionsData = [
    {
        name: "Promo Accesorios",
        image: "/img/promotion/gift.png",
        description: "Descubre los sabores de Brinca Manía, donde cada bocado es una aventura.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$20.00"
    },
    {
        name: "Promo BrincaClaus",
        image: "/img/promotion/gift.png",
        description: "PROXIMAMENTE !!.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$25.00"
    },
    {
        name: "Promo Duo",
        image: "/img/promotion/gift.png",
        description: "Deléitate con nuestros irresistibles pancakes, perfectos para cualquier momento del día.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$15.00"
    },
    {
        name: "Promo Escolar",
        image: "/img/promotion/gift.png",
        description: "Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$30.00"
    },
    {
        name: "Promo Familiar",
        image: "/img/promotion/gift.png",
        description: "Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$35.00"
    },
    {
        name: "Promo Kinder",
        image: "/img/promotion/gift.png",
        description: "Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$28.00"
    },
    {
        name: "Promo 2x1",
        image: "/img/promotion/gift.png",
        description: "Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$18.00"
    },
    {
        name: "Promo Racha",
        image: "/img/promotion/gift.png",
        description: "Vive la magia de disney con los increíbles bolsos y morrales del maravilloso mundo de disney!.",
        contact: {
            name: "Brinca Este 2024 C.A",
            phone: "(+58) 412-3508826",
            email: "tickets@parquebrincaeste.com"
        },
        cost: "$22.00"
    }
];

const Promotion = () => {
    // Estado para controlar qué promoción se muestra actualmente
    const [currentPromoIndex, setCurrentPromoIndex] = useState(0);

    const goToNextPromo = () => {
        setCurrentPromoIndex((prevIndex) =>
            (prevIndex + 1) % promotionsData.length
        );
    };

    const goToPrevPromo = () => {
        setCurrentPromoIndex((prevIndex) =>
            (prevIndex - 1 + promotionsData.length) % promotionsData.length
        );
    };

    const currentPromotion = promotionsData[currentPromoIndex];

    return (
        <Layout>
            <BannerHero title="PROMOCIONES EXCLUSIVAS" />
            <div className="py-section container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="relative bg-white rounded-xl shadow-2xl p-6 md:p-8 lg:p-10 max-w-3xl mx-auto flex flex-col items-center text-center">
                    {/* Botones de navegación (estilo moderno) */}
                    <button
                        onClick={goToPrevPromo}
                        className="absolute left-4 top-1/2 -translate-y-1/2 bg-pink-600 hover:bg-pink-700 text-white p-2 rounded-full shadow-md transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-75 z-10"
                        aria-label="Promoción Anterior"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button
                        onClick={goToNextPromo}
                        className="absolute right-4 top-1/2 -translate-y-1/2 bg-pink-600 hover:bg-pink-700 text-white p-2 rounded-full shadow-md transition-all duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-75 z-10"
                        aria-label="Siguiente Promoción"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    {/* Contenido de la promoción actual */}
                    <div className="flex flex-col items-center w-full">
                        {/* Imagen de la promoción */}
                        <div className="mb-6 relative w-48 h-48 md:w-64 md:h-64 rounded-full overflow-hidden border-4 border-pink-500 shadow-xl flex items-center justify-center bg-gray-50">
                            <img
                                src={currentPromotion.image}
                                alt={currentPromotion.name}
                                className="w-full h-full object-cover p-4" // Añadido p-4 para margen interno si la imagen lo necesita
                            />
                        </div>

                        {/* Nombre de la promoción */}
                        <h2 className="text-4xl font-extrabold text-pink-700 mb-4 animate-fade-in">
                            {currentPromotion.name}
                        </h2>

                        {/* Descripción */}
                        <p className="text-gray-700 text-lg mb-6 max-w-prose leading-relaxed animate-fade-in-up">
                            {currentPromotion.description}
                        </p>

                        {/* Costo */}
                        <div className="bg-pink-100 text-pink-800 text-3xl font-bold py-3 px-8 rounded-full mb-6 shadow-md animate-zoom-in">
                            {currentPromotion.cost}
                        </div>

                        {/* Información de Contacto */}
                        <div className="text-gray-600 text-base space-y-1 animate-fade-in-up delay-100">
                            <p>
                                <span className="font-semibold">Empresa:</span> {currentPromotion.contact.name}
                            </p>
                            <p>
                                <span className="font-semibold">Teléfono:</span>{" "}
                                <a href={`tel:${currentPromotion.contact.phone}`} className="text-blue-600 hover:underline">
                                    {currentPromotion.contact.phone}
                                </a>
                            </p>
                            <p>
                                <span className="font-semibold">Correo:</span>{" "}
                                <a href={`mailto:${currentPromotion.contact.email}`} className="text-blue-600 hover:underline">
                                    {currentPromotion.contact.email}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                {/* Paginación visual (puntos) */}
                <div className="flex justify-center mt-8 space-x-2">
                    {promotionsData.map((_, index) => (
                        <button
                            key={index}
                            onClick={() => setCurrentPromoIndex(index)}
                            className={`w-3 h-3 rounded-full transition-colors duration-300 ${
                                index === currentPromoIndex ? "bg-pink-600" : "bg-gray-300 hover:bg-gray-400"
                            }`}
                            aria-label={`Ir a la promoción ${index + 1}`}
                        ></button>
                    ))}
                </div>
            </div>
            <FrequentlyAsked />
        </Layout>
    );
};

export default Promotion;