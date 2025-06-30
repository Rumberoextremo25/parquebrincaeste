import React from "react";
import { BuildingOffice2Icon, GlobeAmericasIcon, PresentationChartBarIcon, UserGroupIcon } from "@heroicons/react/24/outline"; // Importando los íconos necesarios

// Componente TitleSection Simplificado (incrustado para evitar errores de importación)
const TitleSection = ({ title, subTitle, description, className }) => {
    return (
        <div className={`mb-8 ${className}`}>
            {subTitle && (
                <p className="text-sm font-semibold uppercase text-blue-600 mb-2">
                    {subTitle}
                </p>
            )}
            <h2 className="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                {title}
            </h2>
            {description && (
                <p className="text-md text-gray-600 max-w-2xl mx-auto">
                    {description}
                </p>
            )}
        </div>
    );
};

// Componente IconMetric (se mantiene y se estiliza mejor)
const IconMetric = ({ Icon, title, metric }) => {
    return (
        <div className="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg border border-gray-100 transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
            <div className="mb-4">
                <Icon className="text-blue-500 h-16 w-16 drop-shadow-md" alt={title} />
            </div>
            <h3 className="mt-2 text-4xl font-extrabold text-gray-900">{metric}</h3>
            <span className="mt-2 text-lg font-medium text-gray-600 text-center">
                {title}
            </span>
        </div>
    );
};


const Section3 = () => {
    return (
        <>
            {/* Sección 1: Celebración de Cumpleaños */}
            <section className="bg-gradient-to-br from-purple-50 to-pink-100 py-16 sm:py-24 rounded-3xl shadow-xl mx-auto container px-4 mb-20">
                <div className="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
                    {/* Columna de Texto */}
                    <div className="lg:w-1/2 text-center lg:text-left">
                        <TitleSection
                            title="Celebra tu cumpleaños con nosotros"
                            subTitle="FIESTAS INOLVIDABLES"
                            description="¡Haz de tu cumpleaños una aventura épica en nuestro Parque de Camas Elásticas! Ofrecemos paquetes diseñados para todas las edades y deseos."
                            className="mb-8"
                        />
                        <div className="mt-8 space-y-6 text-lg text-gray-700 leading-relaxed">
                            <p>
                                ¡Prepárate para saltos y diversión sin límites en nuestro increíble Parque de Camas Elásticas! Hemos diseñado tres emocionantes paquetes para tu fiesta:
                            </p>
                            <ul className="list-disc list-inside space-y-2 pl-4">
                                <li>El **Plan Básico**, para una celebración sencilla pero llena de energía.</li>
                                <li>El **Plan Medio**, con extras para hacer tu día aún más especial y personalizado.</li>
                                <li>Y el **Plan VIP**, la experiencia completa con todas las sorpresas, decoraciones y beneficios que puedas imaginar para una fiesta de lujo.</li>
                            </ul>
                            <p>
                                ¡Elige el plan que mejor se adapte a tus sueños y prepárate para un cumpleaños inolvidable que todos recordarán!
                            </p>
                        </div>
                        <div className="mt-10 text-center lg:text-left">
                            {/* Se ha cambiado Link por un <a> tag estándar.
                                La función `route()` no está disponible en este contexto de compilación
                                de un solo archivo. Por favor, reemplaza '#your-birthday-packages-page'
                                con la URL real de tu página de cumpleaños si la conoces. */}
                            <a href="/package" className="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold text-lg rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-400 focus:ring-opacity-75">
                                Ver Paquetes de Cumpleaños
                                <svg className="ml-3 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fillRule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clipRule="evenodd"></path><path fillRule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clipRule="evenodd"></path></svg>
                            </a>
                        </div>
                    </div>

                    {/* Columna de Imagen */}
                    <div className="lg:w-1/2 w-full">
                        <img
                            src="/img/about/cumpleaños.webp"
                            alt="Celebración de Cumpleaños en Brinca Este"
                            className="w-full h-96 object-cover rounded-2xl shadow-2xl transition-transform duration-500 ease-in-out hover:scale-105"
                            loading="lazy"
                            onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/600x400/FFC0CB/808080?text=Imagen+de+Cumpleaños"; }}
                        />
                    </div>
                </div>
            </section>
        </>
    );
};

export default Section3;
