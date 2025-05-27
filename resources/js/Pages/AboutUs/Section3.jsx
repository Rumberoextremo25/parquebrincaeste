import TitleSection from "@/Components/TitleSection";
import { BuildingOffice2Icon, GlobeAmericasIcon, PresentationChartBarIcon, UserGroupIcon } from "@heroicons/react/24/outline";
import React, { useEffect, useState } from "react";


const Section3 = () => {
    return (

        <div className="py-section container">
            <div className="grid gap-24 lg:grid-cols-2">
                <div>
                    <TitleSection title="Celebra tu cumpleaños con nosotros" subTitle="" />
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                        ¡Celebra tu cumpleaños con saltos y diversión en nuestro Parque de Camas Elásticas! 
                        Ofrecemos tres emocionantes paquetes para tu fiesta: Plan Básico, 
                        para una diversión sencilla pero emocionante; Plan Medio, con extras para hacer tu día aún más especial; 
                        y el Plan VIP, la experiencia completa con todas las sorpresas que puedas imaginar. 
                        </p>
                        <p className="text">
                        ¡Elige el plan que más te guste y prepárate para un cumpleaños inolvidable!
                        </p>
                    </div>
                </div>
                <div className="hidden lg:block">
                    <img
                        src="/img/about/cumpleaños.webp"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
            </div>
        </div>

    );
};

const IconMetric = ({ Icon, title, metric }) => {
    return (
        <div className="col-span-6 text-center md:col-span-3 lg:col-span-2">
            <div className="flex flex-col items-center">
                <div>
                    <Icon className="text-blue-400 h-16 w-16" alt={title} />
                </div>
                <h3 className="mt-4 font-semibold lg:mt-5">{metric}</h3>
                <span className="font-medium text-blue-400 lg:mt-3">
                    {title}
                </span>
            </div>
        </div>
    );
};

export default Section3;

//terminar responsive "hechos graciosos"
