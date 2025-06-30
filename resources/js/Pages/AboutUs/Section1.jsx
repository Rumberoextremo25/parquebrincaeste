import React from "react";
import { Link } from "@inertiajs/react"; // Se mantiene, ya que es parte de tu configuración de Inertia.js

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

const Section1 = () => {
    return (
        <section className="bg-gradient-to-br from-blue-50 to-indigo-100 py-16 sm:py-24 rounded-3xl shadow-xl">
            <div className="container mx-auto px-4">
                <div className="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
                    {/* Columna de Texto - A la izquierda por defecto */}
                    <div className="lg:w-1/2 text-center lg:text-left">
                        <TitleSection
                            title="Somos el Parque de Trampolines más grande de Caracas"
                            subTitle="BIENVENIDO A BRINCA ESTE"
                            description="Nuestro objetivo es llevar la diversión a otro nivel, donde el entretenimiento y la imaginación te llevarán a brincar tan alto como lo sueñen."
                            className="mb-8" // Añadimos margen inferior para separar del contenido
                        />
                        <div className="mt-8 space-y-6 text-lg text-gray-700 leading-relaxed">
                            <p>
                                En Brinca Este, no solo encontrarás el parque de trampolines más grande de Caracas, sino un universo de emociones. Contamos con áreas de recreación infantil diseñadas especialmente para los más pequeños, desde los 10 meses hasta los 5 años, asegurando un espacio seguro y divertido para sus primeras aventuras.
                            </p>
                            <p>
                                Para los más grandes, desde los 6 años en adelante, nuestras áreas están repletas de desafíos y entretenimiento que te harán saltar, correr y explorar sin límites. Además, nuestros aliados harán tu estancia inolvidable con diversos espacios gastronómicos llenos de sabores que deleitarán tu paladar y tiendas de souvenirs donde podrás llevar un pedacito de Brinca Este contigo.
                            </p>
                        </div>
                        <div className="mt-10 text-center lg:text-left">
                            {/* Se usa Link de Inertia.js. Si este sigue dando error, por favor,
                                asegúrate de que '@inertiajs/react' esté correctamente instalado
                                y que tu entorno de compilación lo reconozca. */}
                            <Link href={route('contact_us')} className="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-lg rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-400 focus:ring-opacity-75">
                                Conoce Más Sobre Nosotros
                                <svg className="ml-3 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fillRule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clipRule="evenodd"></path><path fillRule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clipRule="evenodd"></path></svg>
                            </Link>
                        </div>
                    </div>

                    {/* Columna de Imagen - A la derecha por defecto */}
                    <div className="lg:w-1/2 w-full">
                        <img
                            src="/img/about/IMG_2749.WEBP" // Asegúrate de que esta ruta sea correcta
                            alt="Parque de Trampolines Brinca Este"
                            className="w-full h-96 object-cover rounded-2xl shadow-2xl transition-transform duration-500 ease-in-out hover:scale-105"
                            loading="lazy" // Carga perezosa para mejor rendimiento
                            onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/600x400/D1E9FF/808080?text=Imagen+del+Parque"; }} // Fallback de imagen
                        />
                    </div>
                </div>
            </div>
        </section>
    );
};

export default Section1;
