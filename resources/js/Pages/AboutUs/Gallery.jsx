import SectionHeader from "@/Components/SectionHeader";
import FsLightbox from "fslightbox-react";
import React, { useState, useEffect, useRef } from "react";

const Gallery = () => {
    const imgGallery = [
        "/img/about/IMG_2748.WEBP",
        "/img/about/IMG_2749.WEBP",
        "/img/about/IMG_2750.WEBP",
        "/img/about/IMG_2752.WEBP",
        "/img/about/IMG_2753.WEBP",
        "/img/about/IMG_2754.WEBP",
        "/img/about/IMG_2755.WEBP",
        "/img/about/IMG_2756.WEBP",
        "/img/about/IMG_3132.WEBP",
        "/img/about/IMG_8396.WEBP",
        "/img/about/IMG_8397.WEBP",
    ];

    const [currentSlide, setCurrentSlide] = useState(0); // Estado para la diapositiva actual
    const carouselRef = useRef(null); // Referencia para el elemento del carrusel

    // Configuración para el carrusel (ajustable)
    const slidesToShow = 3; // Cuántas imágenes se muestran a la vez en escritorio
    const slidesToScroll = 1; // Cuántas imágenes se desplazan por cada navegación
    const autoplayInterval = 3000; // Intervalo de auto-reproducción en ms

    // Lógica para el auto-reproducción
    useEffect(() => {
        const interval = setInterval(() => {
            nextSlide();
        }, autoplayInterval);
        return () => clearInterval(interval); // Limpiar el intervalo al desmontar el componente
    }, [currentSlide]); // Reiniciar el intervalo cada vez que el slide cambia

    const nextSlide = () => {
        setCurrentSlide((prev) => (prev + slidesToScroll) % imgGallery.length);
    };

    const prevSlide = () => {
        setCurrentSlide((prev) => (prev - slidesToScroll + imgGallery.length) % imgGallery.length);
    };

    const goToSlide = (index) => {
        setCurrentSlide(index);
    };

    // Lógica para el manejo responsivo del número de slides visibles
    // Esto es una simulación básica, en un entorno real con Tailwind CSS se usarían sus breakpoints directamente en las clases.
    // Para un carrusel manual, necesitarías ajustar slidesToShow dinámicamente con useState y useEffect para escuchar window.innerWidth.
    // Para simplificar y mantenerlo autocontenido, la visibilidad se basará en CSS de Tailwind.

    return (
        <div className="py-16 container mx-auto px-4"> {/* Ajuste del contenedor principal con mx-auto y px-4 para centrado y padding */}
            <SectionHeader
                subTitle=""
                title="UNA ENTRADA PARA CADA AFICIONADO DE LA DIVERSION."
                text=""
            />

            <div className="relative mt-14 max-w-6xl mx-auto overflow-hidden rounded-lg shadow-xl"> {/* Contenedor para el carrusel */}
                {/* Carrusel de Imágenes */}
                <div 
                    ref={carouselRef}
                    className="flex transition-transform duration-500 ease-in-out"
                    // Calcula el desplazamiento basado en el slide actual y el número total de imágenes
                    // Multiplica el porcentaje de cada imagen (100% / imgGallery.length) por el slide actual.
                    style={{ transform: `translateX(-${(currentSlide * 100) / imgGallery.length}%)` }} 
                >
                    {imgGallery.map((imgSrc, index) => (
                        <div 
                            key={index} 
                            // w-full para móviles, sm:w-1/2 para tablets, lg:w-1/3 para escritorio (simula slidesToShow)
                            // La clase flex-shrink-0 es crucial para evitar que las imágenes se encojan.
                            className="w-full sm:w-1/2 lg:w-1/3 flex-shrink-0 p-2" 
                        >
                            <img
                                className="h-64 w-full object-cover rounded-md transition duration-300 transform hover:scale-105" // Altura fija y efecto hover
                                src={imgSrc}
                                alt={`Galería de Brinca Este - Imagen ${index + 1}`}
                                loading="lazy" // Lazy Loading nativo
                                onError={(e) => { e.target.onerror = null; e.target.src = "https://placehold.co/500x300/e0e0e0/ffffff?text=Imagen+No+Disponible"; }} // Fallback de imagen
                            />
                        </div>
                    ))}
                </div>

                {/* Botones de Navegación (Flechas) */}
                <button
                    onClick={prevSlide}
                    className="absolute top-1/2 left-4 transform -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-2 rounded-full z-10 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    &#10094; {/* Flecha izquierda */}
                </button>
                <button
                    onClick={nextSlide}
                    className="absolute top-1/2 right-4 transform -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-2 rounded-full z-10 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    &#10095; {/* Flecha derecha */}
                </button>

                {/* Puntos de Navegación (Dots) */}
                <div className="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-10">
                    {imgGallery.map((_, index) => (
                        <button
                            key={index}
                            onClick={() => goToSlide(index)}
                            className={`h-3 w-3 rounded-full ${
                                index === currentSlide ? 'bg-white' : 'bg-gray-400'
                            } hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500`}
                        ></button>
                    ))}
                </div>
            </div>

            {/* FsLightbox y react-slick se han eliminado para resolver errores de compilación de módulos externos.
                Si necesitas una funcionalidad de lightbox o un carrusel más avanzado, considera implementar
                una solución autocontenida o asegurar que las librerías estén correctamente instaladas y resueltas en tu entorno de construcción. */}
        </div>
    );
};

export default Gallery;