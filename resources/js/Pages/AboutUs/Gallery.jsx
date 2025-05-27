import SectionHeader from "@/Components/SectionHeader";
import FsLightbox from "fslightbox-react";
import React, { useState } from "react";

const Gallery = () => {
    const imgGallery = [
        "/img/about/IMG_2750.jpg",
        "/img/about/IMG_2756.jpg",
        "/img/about/IMG_2754.jpg",
        "/img/about/IMG_2751.jpg",
        "/img/about/IMG_2752.jpg",
        "/img/about/IMG_2753.jpg",
        "/img/about/IMG_2747.jpg",
        "/img/about/IMG_2755.jpg",
        "/img/about/BrincaEste.jpg",
        "img/about/IMG_8396.jpg",
        "img/about/IMG_8397.jpg",
        "img/about/IMG_3132.jpg",
        "img/about/IMG_8643.jpg"
    ];
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
                title="UNA ENTRADA PARA CADA AFICIONADO  DE LA DIVERSION."
                text=""
            />

            <div className="mt-14">
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-4">
                    {imgGallery.map((img, key) => (
                        <div
                            key={key}
                            onClick={() => openLightboxOnSlide(key + 1)}
                            className={
                                "cursor-pointer overflow-hidden " +
                                (key === 1 ? "md:col-span-2 md:row-span-2" : "")
                            }
                        >
                            <img
                                className="h-full w-full rounded-md object-cover transition duration-200 hover:scale-110"
                                src={img}
                                alt=""
                            />
                        </div>
                    ))}
                </div>
            </div>
            <FsLightbox
                toggler={lightboxController.toggler}
                sources={imgGallery}
                slide={lightboxController.slide}
            />
        </div>
    );
};

export default Gallery;