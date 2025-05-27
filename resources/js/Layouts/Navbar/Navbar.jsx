import React from "react";
import DesktopNavbar from "./DesktopNavbar";
import MovileNavbar from "./MovileNavbar/MovileNavbar";

const Navbar = () => {
    let navigation = [

        {
            title: "Promociones",
            path: route("promotion"),
            current: route().current("promotion"),
            hidenMovil: true,
        },

        {
            title: "Tienda",
            path: route("tienda"),
            current: route().current("tienda"),
            hidenMovil: true,
        },

        {
            title: "Paquetes",
            path: route("package", ),
            current: route().current("package"),
            hidenMovil: true,
        },

        {
            title: "Sobre Nosotros",
            path: route("about_us"),
            current: route().current("about_us"),
        },
        {
            title: "Contáctenos",
            path: route("contact_us"),
            current: route().current("contact_us"),
        },
    ];
    return (
        <>
            <MovileNavbar navigation={navigation} />
            <DesktopNavbar navigation={navigation} />
        </>
    );
};

export default Navbar;
