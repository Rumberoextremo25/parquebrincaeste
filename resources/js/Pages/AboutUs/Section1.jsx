import TitleSection from "@/Components/TitleSection";
import { Link } from "@inertiajs/react";
import React from "react";

const Section1 = () => {
    return (
        <div className="py-section container">
            <div className="grid gap-24 lg:grid-cols-2">
                <div>
                    <TitleSection title="Somos el Parque de Trampolines más grande de Caracas" subTitle="" />
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                        Somos el parque de trampolines más grande de Caracas, nuestro objetivo es llevar la diversión a otro nivel, 
                        donde el entretenimiento e imaginación los llevará a brincar tan alto como lo sueñen. 
                        Contamos con áreas de recreación infantil para niños de 10 meses hasta 5 años. 
                        </p>
                        <p className="text">
                        Y áreas para edades más avanzadas desde los 6 años en adelante. 
                        Nuestros aliados harán inolvidable tu estancia, además contamos con diferentes espacios gastronómicos 
                        llenos de sabores y souvenirs que te llevarán a soñar.
                        </p>
                    </div>
                </div>
                <div className="hidden lg:block">
                    <img
                        src="/img/about/IMG_2749.jpg"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
            </div>
        </div>
    );
};

export default Section1;
