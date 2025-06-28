import SectionHeader from "@/Components/SectionHeader";
import FsLightbox from "fslightbox-react";
import React, { useState } from "react";

const Gallery = () => {
    // Idealmente, estas imágenes deberían estar optimizadas (comprimidas y redimensionadas)
    // y, si es posible, en formatos modernos como WebP.
    const imgGallery = [
        "/img/about/IMG_2750.JPG",
        "/img/about/IMG_2756.JPG",
        "/img/about/IMG_2754.JPG",
        "/img/about/IMG_2751.JPG",
        "/img/about/IMG_2752.JPG",
        "/img/about/IMG_2753.JPG",
        "/img/about/IMG_2747.JPG",
        "/img/about/IMG_2755.JPG",
        "/img/about/IMG_8396.JPG",
        "/img/about/IMG_8397.JPG",
        "/img/about/IMG_3132.JPG",
        "/img/about/IMG_8643.JPG"
    ];

    // // Ejemplo de cómo podrías estructurar para diferentes tamaños (más avanzado)
    // // Esto requiere tener diferentes versiones de tus imágenes (miniatura y tamaño completo).
    // const optimizedImgGallery = imgGallery.map(src => ({
    //     thumbnail: src.replace('.png', '_thumb.webp'), // Pequeña y optimizada para la cuadrícula
    //     full: src // Original o tamaño completo optimizado para el lightbox
    // }));

    const [lightboxController, setLightboxController] = useState({
        toggler: false,
        slide: 0,
    });

    function openLightboxOnSlide(number) {
        setLightboxController({
            toggler: !lightboxController.toggler,
            slide: number,
        });
    }

    return (
        <div className="py-section container">
            <SectionHeader
                subTitle=""
                title="UNA ENTRADA PARA CADA AFICIONADO DE LA DIVERSION."
                text=""
            />

            <div className="mt-14">
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-4">
                    {imgGallery.map((imgSrc, key) => ( // Cambié 'img' a 'imgSrc' para mayor claridad
                        <div
                            key={key}
                            onClick={() => openLightboxOnSlide(key + 1)}
                            className={
                                "cursor-pointer overflow-hidden group " + // Añadí 'group' para el efecto hover
                                (key === 1 ? "md:col-span-2 md:row-span-2" : "")
                            }
                        >
                            <img
                                className="h-full w-full rounded-md object-cover transition duration-200 group-hover:scale-110" // Aplicado hover a la imagen
                                src={imgSrc} // Usamos imgSrc
                                alt={`Galería de Brinca Este - Imagen ${key + 1}`} // Alt text descriptivo
                                loading="lazy" // *** Implementación clave de Lazy Loading ***
                                width="500" // *** Sugerencia para optimización de tamaño (ajústalo) ***
                                height="300" // *** Sugerencia para optimización de tamaño (ajústalo) ***
                            />
                        </div>
                    ))}
                </div>
            </div>
            <FsLightbox
                toggler={lightboxController.toggler}
                sources={imgGallery} // FsLightbox utiliza las fuentes originales, lo cual es correcto
                slide={lightboxController.slide}
            />
        </div>
    );
};

export default Gallery;