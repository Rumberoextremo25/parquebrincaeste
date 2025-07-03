import React, { useState, useEffect } from "react";
import { router } from "@inertiajs/react";
import Layout from "@/Layouts/Layout";
import BannerHero from "@/Components/Hero/BannerHero";

const Tienda = (props) => {
    // Inicializa PRODUCTS desde props, asegurando que el precio sea flotante
    const [PRODUCTS, setPRODUCTS] = useState(
        props?.products.map((item) => ({
            ...item,
            price: parseFloat(item.price),
        })) ?? []
    );

    // --- Estados para el formulario de selección ---
    const getDefaultValues = () => {
        const today = new Date();
        return {
            fecha: today.toLocaleDateString("en-CA", {
                // "en-CA" para formato YYYY-MM-DD
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            }),
        };
    };

    const defaultValue = getDefaultValues();

    const [fecha, setFecha] = useState(defaultValue.fecha);
    const [mensaje, setMensaje] = useState("");

    const [clientType, setClientType] = useState(null); // 'adultOrOver6' o 'under6'
    const [selectedBraceletId, setSelectedBraceletId] = useState("");
    const [braceletQuantity, setBraceletQuantity] = useState(1);

    const [selectedSockTallaId, setSelectedSockTallaId] = useState("");
    const [sockQuantity, setSockQuantity] = useState(1); // Cantidad de calcetines fija en 1

    // Estado para la hora seleccionada
    const [selectedTime, setSelectedTime] = useState("");

    // --- Estado para el carrito de compras ---
    const [cartItems, setCartItems] = useState([]);

    // --- Productos derivados para facilitar el acceso ---
    // Función para obtener el día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
    const getDayOfWeek = (dateString) => {
        const date = new Date(dateString + "T12:00:00"); // Añade T12:00:00 para evitar problemas de zona horaria
        return date.getDay();
    };

    // Determina si la fecha seleccionada es de fin de semana (Sábado, Domingo)
    const isWeekend =
        getDayOfWeek(fecha) === 6 || getDayOfWeek(fecha) === 0; // 6=Sábado, 0=Domingo
    // Determina si la fecha seleccionada es de entre semana (Lunes a Viernes)
    const isWeekday = getDayOfWeek(fecha) >= 1 && getDayOfWeek(fecha) <= 5; // 1=Lunes, 2=Martes, ..., 5=Viernes

    // Precio base de los brazaletes según el día
    const BRACELET_PRICE_WEEKDAY = 5.0; // Lunes a Viernes
    const BRACELET_PRICE_WEEKEND = 6.0; // Sábado y Domingo
    const BABY_PARK_PRICE = 6.0; // Costo fijo para Baby Park todos los días

    // Mapeo de franjas horarias a colores de brazaletes (replicando la lógica del seeder PHP)
    const timeColorMap = {
        '11:00 AM a 12:00 PM': 'Azul',
        '12:00 PM a 1:00 PM': 'Amarillo',
        '1:00 PM a 2:00 PM': 'Rojo',
        '2:00 PM a 3:00 PM': 'Verde Manzana',
        '3:00 PM a 4:00 PM': 'Naranja',
        '4:00 PM a 5:00 PM': 'Morado',
        '5:00 PM a 6:00 PM': 'Negro',
        '6:00 PM a 7:00 PM': 'Vinotinto',
        '7:00 PM a 8:00 PM': 'Azul Rey',
        '8:00 PM a 9:00 PM': 'Azul Marino',
    };

    // Encuentra el producto "Pass Baby Park"
    const baseBabyParkBraceletProduct = PRODUCTS.find(
        (p) => p.category === "Pass Baby Park"
    );

    // Encuentra el producto "Brazalete" (general para trampolines)
    const baseTrampolineBraceletProduct = PRODUCTS.find(
        (p) => p.category === "Brazalete" && !p.name.includes("Brazalete Hora")
    );

    // Crea un objeto del brazalete Baby Park con el precio ajustado (fijo a $6)
    const adjustedBabyParkBraceletProduct = baseBabyParkBraceletProduct
        ? {
              ...baseBabyParkBraceletProduct,
              price: BABY_PARK_PRICE, // PRECIO FIJO DE $6
          }
        : null;

    // Brazalete de Trampolines ajustado por precio (según día de la semana)
    const adjustedTrampolineBraceletProduct = baseTrampolineBraceletProduct
        ? {
              ...baseTrampolineBraceletProduct,
              price: isWeekend
                  ? BRACELET_PRICE_WEEKEND
                  : BRACELET_PRICE_WEEKDAY,
          }
        : null;

    // Filtra las medias (calcetines)
    const socksProducts = PRODUCTS.filter((p) => p.category === "Medias");

    // selectedBraceletProduct ahora se obtiene de los productos ajustados
    const selectedBraceletProduct =
        adjustedBabyParkBraceletProduct?.id === selectedBraceletId
            ? adjustedBabyParkBraceletProduct
            : adjustedTrampolineBraceletProduct?.id === selectedBraceletId
            ? adjustedTrampolineBraceletProduct
            : null;

    const selectedSockProduct = PRODUCTS.find(
        (p) => p.id === selectedSockTallaId
    );

    // --- Handlers de selección de formulario ---
    const handleClientTypeChange = (type) => {
        setClientType(type);
        setSelectedBraceletId("");
        setBraceletQuantity(1);
        setSelectedSockTallaId("");
        setSockQuantity(1);
        setSelectedTime("");

        if (type === "under6" && adjustedBabyParkBraceletProduct) {
            setSelectedBraceletId(adjustedBabyParkBraceletProduct.id);
            setBraceletQuantity(1);
        } else if (type === "adultOrOver6" && adjustedTrampolineBraceletProduct) {
            setSelectedBraceletId(adjustedTrampolineBraceletProduct.id);
        }
    };

    useEffect(() => {
        const isAnyBraceletSelected = selectedBraceletId !== "";

        if (isAnyBraceletSelected && socksProducts.length > 0) {
            const currentSelectedSockApplies = socksProducts.some(
                (sock) => sock.id === selectedSockTallaId
            );

            if (!selectedSockTallaId || !currentSelectedSockApplies) {
                setSelectedSockTallaId(String(socksProducts[0].id));
            }
            setSockQuantity(1);
        } else if (!isAnyBraceletSelected) {
            setSelectedSockTallaId("");
            setSockQuantity(1);
        }
    }, [selectedBraceletId, socksProducts, selectedSockTallaId]);

    useEffect(() => {
        if (selectedBraceletId) {
            let isValidSelection = false;
            if (selectedBraceletId === adjustedBabyParkBraceletProduct?.id) {
                isValidSelection = !!adjustedBabyParkBraceletProduct;
            } else if (selectedBraceletId === adjustedTrampolineBraceletProduct?.id) {
                isValidSelection = !!adjustedTrampolineBraceletProduct;
            }

            if (!isValidSelection) {
                setSelectedBraceletId("");
                setSelectedTime("");
            }
        }

        if (
            clientType === "under6" &&
            adjustedBabyParkBraceletProduct &&
            selectedBraceletId !== adjustedBabyParkBraceletProduct.id
        ) {
            setSelectedBraceletId(adjustedBabyParkBraceletProduct.id);
            setSelectedTime("");
        } else if (
            clientType === "adultOrOver6" &&
            adjustedTrampolineBraceletProduct &&
            selectedBraceletId !== adjustedTrampolineBraceletProduct.id
        ) {
            setSelectedBraceletId(adjustedTrampolineBraceletProduct.id);
            setSelectedTime("");
        }
    }, [
        fecha,
        isWeekday,
        isWeekend,
        PRODUCTS,
        clientType,
        selectedBraceletId,
        adjustedBabyParkBraceletProduct,
        adjustedTrampolineBraceletProduct,
    ]);

    // Generar opciones de hora en formato "HH:MM a HH:MM"
    const generateTimeOptions = () => {
        const options = [];
        for (let hour = 11; hour <= 20; hour++) { // De 11 AM (11) a 8 PM (20) para que el último sea 9 PM (21)
            const startHour = hour;
            const endHour = hour + 1;

            const formatHour = (h) => {
                const period = h < 12 || h === 24 ? "AM" : "PM";
                const displayHour = h % 12 === 0 ? 12 : h % 12;
                return `${displayHour}:00 ${period}`;
            };

            options.push(`${formatHour(startHour)} a ${formatHour(endHour)}`);
        }
        return options;
    };

    // --- Lógica para añadir ítems al carrito ---
    const handleAddToCart = () => {
        setMensaje("");

        const dayOfWeekForValidation = getDayOfWeek(fecha);

        if (!isWeekday && !isWeekend) {
            setMensaje(
                "No hay brazaletes disponibles para la fecha seleccionada. Por favor, elige una fecha válida (Lunes a Domingo)."
            );
            return;
        }

        if (!selectedBraceletProduct) {
            setMensaje("Por favor, selecciona un brazalete antes de añadir.");
            return;
        }

        if (clientType === "adultOrOver6" && !selectedTime) {
            setMensaje("Por favor, selecciona una hora para el brazalete.");
            return;
        }

        if (selectedBraceletProduct && !selectedSockProduct) {
            setMensaje(
                "Las medias son obligatorias con la compra de un brazalete. Por favor, selecciona una talla."
            );
            return;
        }

        const itemsToAdd = [];

        const braceletPrice = selectedBraceletProduct.price;
        let braceletDisplayName = selectedBraceletProduct.name; // Default to the product's base name

        // MODIFICACIÓN CLAVE: Ajustar el nombre del brazalete si es de trampolines y se seleccionó una hora
        if (clientType === "adultOrOver6" && selectedTime) {
            const color = timeColorMap[selectedTime];
            if (color) {
                // Assuming the base name is "Brazalete" and we want to append the color
                braceletDisplayName = `Brazalete ${color}`;
            }
        }

        const braceletCartItem = {
            uniqueId: Date.now() + "-bracelet-" + selectedBraceletProduct.id + (selectedTime ? "-" + selectedTime.replace(/\s/g, '').replace(/:/g, '') : ''),
            product: { // Mantenemos el objeto product aquí para uso interno de Tienda.jsx
                id: selectedBraceletProduct.id,
                name: braceletDisplayName, // Usar el nombre dinámicamente generado
                description: selectedBraceletProduct.description,
                price: braceletPrice, // Este es el precio ajustado
            },
            quantity: braceletQuantity,
            selectedDate: fecha,
            clientType: clientType,
            selectedTime: selectedTime,
        };
        itemsToAdd.push(braceletCartItem);

        if (selectedSockProduct) {
            const sockCartItem = {
                uniqueId: Date.now() + "-sock-" + selectedSockProduct.id + (selectedTime ? "-" + selectedTime.replace(/\s/g, '').replace(/:/g, '') : ''),
                product: { // Mantenemos el objeto product para los calcetines también
                    id: selectedSockProduct.id,
                    name: selectedSockProduct.name,
                    description: selectedSockProduct.description,
                    price: selectedSockProduct.price,
                },
                quantity: sockQuantity,
                // MODIFICACIÓN CLAVE: Añadir los campos que Laravel espera para todos los ítems
                selectedDate: null, // No aplica para calcetines
                selectedTime: null, // No aplica para calcetines
                clientType: null, // No aplica para calcetines
                product_description: selectedSockProduct.description, // Usar la descripción del calcetín
            };
            itemsToAdd.push(sockCartItem);
        }

        setCartItems((prevCartItems) => [...prevCartItems, ...itemsToAdd]);
        setMensaje("¡Productos añadidos al carrito!");

        // Reiniciar el formulario de selección después de añadir al carrito
        setClientType(null);
        setSelectedBraceletId("");
        setBraceletQuantity(1);
        setSelectedSockTallaId("");
        setSockQuantity(1);
        setSelectedTime("");
    };

    // --- Lógica para eliminar un ítem del carrito ---
    const handleRemoveFromCart = (uniqueIdToRemove) => {
        setCartItems((prevCartItems) =>
            prevCartItems.filter((item) => item.uniqueId !== uniqueIdToRemove)
        );
    };

    // --- Lógica para ajustar la cantidad de un ítem en el carrito ---
    const handleUpdateCartItemQuantity = (uniqueIdToUpdate, newQuantity) => {
        setCartItems((prevCartItems) =>
            prevCartItems.map((item) =>
                item.uniqueId === uniqueIdToUpdate
                    ? { ...item, quantity: Math.max(1, newQuantity) }
                    : item
            )
        );
    };

    // --- Lógica para el submit final del carrito a Laravel ---
    const handleSubmitCheckout = () => {
        setMensaje(""); // Limpiar mensaje antes de enviar

        if (cartItems.length === 0) {
            setMensaje("Tu carrito está vacío. Por favor, añade productos.");
            return;
        }

        // Construir el array 'items' con la estructura exacta que tu backend espera
        // Aplanamos las propiedades del producto para que Checkout.jsx las reciba directamente
        const itemsToSubmit = cartItems.map((item) => ({
            product_id: item.product.id,
            quantity: item.quantity,
            price: item.product.price, // Este es el precio ajustado (5$ o 6$)
            selected_date: item.selectedDate,
            selected_time: item.selectedTime || null,
            product_name: item.product.name, // Añadimos el nombre del producto
            product_description: item.product.description, // Añadimos la descripción del producto
            client_type: item.clientType, // Añadimos el tipo de cliente
            uniqueId: item.uniqueId, // Asegúrate de enviar el uniqueId
        }));

        const dataToSend = {
            fecha: fecha, // La fecha principal del formulario
            items: itemsToSubmit,
        };

        router.post("/tienda", dataToSend); // Asegúrate de que esta ruta sea la correcta para tu método 'comprar'
    };

    // Calcular el total del carrito para mostrar en el frontend
    const totalCartPrice = cartItems.reduce(
        (sum, item) => sum + item.product.price * item.quantity,
        0
    );

    return (
        <Layout>
            <BannerHero
                img="/img/home/BrincaEste.jpg"
                title="COMPRA TUS ENTRADAS AQUÍ!"
            />
            <div className="py-8 container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Columna de Selección de Productos */}
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">
                            Selecciona tus Productos
                        </h2>

                        {/* Paso 1: Selección de Fecha */}
                        <div className="form-group mb-6 border-b pb-4">
                            <label
                                htmlFor="fecha"
                                className="block text-gray-700 text-lg font-semibold mb-3"
                            >
                                Fecha de Visita:
                            </label>
                            <input
                                type="date"
                                id="fecha"
                                className="mt-1 block w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                                value={fecha}
                                onChange={(e) => setFecha(e.target.value)}
                                required
                            />
                        </div>

                        {/* Paso 2: Selección de Tipo de Cliente */}
                        <div className="mb-6 border-b pb-4">
                            <p className="block text-gray-700 text-lg font-semibold mb-3">
                                ¿Para quién es el brazalete?
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <button
                                    type="button"
                                    onClick={() =>
                                        handleClientTypeChange("under6")
                                    }
                                    className={`py-3 px-6 rounded-lg font-medium transition duration-300
                                        ${
                                            clientType === "under6"
                                                ? "bg-indigo-600 text-white shadow-md"
                                                : "bg-indigo-100 text-indigo-700 hover:bg-indigo-200"
                                        }
                                    `}
                                >
                                    Niño/a &lt; 6 años (Área Baby Park)
                                </button>
                                <button
                                    type="button"
                                    onClick={() =>
                                        handleClientTypeChange("adultOrOver6")
                                    }
                                    className={`py-3 px-6 rounded-lg font-medium transition duration-300
                                        ${
                                            clientType === "adultOrOver6"
                                                ? "bg-emerald-600 text-white shadow-md"
                                                : "bg-emerald-100 text-emerald-700 hover:bg-emerald-200"
                                        }
                                    `}
                                >
                                    Mayor de 6 años o Adulto (Trampolines)
                                </button>
                            </div>
                        </div>

                        {/* Paso 3: Selección de Brazalete y Medias (Condicional) */}
                        {clientType && (
                            <div className="mt-6">
                                {/* Sección de Brazalete Baby Park */}
                                {clientType === "under6" &&
                                    adjustedBabyParkBraceletProduct && (
                                        <div className="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                                            <h3 className="text-xl font-semibold text-blue-800 mb-3">
                                                Brazalete Baby Park
                                            </h3>
                                            <p className="text-blue-700 mb-2">
                                                **
                                                {
                                                    adjustedBabyParkBraceletProduct.name
                                                }
                                                **:{" "}
                                                {
                                                    adjustedBabyParkBraceletProduct.description
                                                }
                                                <span className="font-bold ml-2">
                                                    ${" "}
                                                    {adjustedBabyParkBraceletProduct.price.toFixed(
                                                        2
                                                    )}
                                                </span>
                                            </p>
                                            <div className="flex items-center gap-2 mt-2">
                                                <label
                                                    htmlFor="babyParkQty"
                                                    className="text-gray-700"
                                                >
                                                    Cantidad:
                                                </label>
                                                <input
                                                    type="number"
                                                    id="babyParkQty"
                                                    min="1"
                                                    value={braceletQuantity}
                                                    onChange={(e) =>
                                                        setBraceletQuantity(
                                                            parseInt(
                                                                e.target.value
                                                            ) || 1
                                                        )
                                                    }
                                                    className="w-20 p-2 border rounded-md text-center"
                                                />
                                            </div>
                                        </div>
                                    )}
                                {/* Si no se encontró el Baby Park para la fecha seleccionada */}
                                {clientType === "under6" &&
                                    !adjustedBabyParkBraceletProduct && (
                                        <p className="text-red-500 text-sm mt-2">
                                            No hay brazaletes Baby Park
                                            disponibles.
                                        </p>
                                    )}

                                {/* Sección de Brazalete y Hora para Adultos/Mayores de 6 */}
                                {clientType === "adultOrOver6" && adjustedTrampolineBraceletProduct && (
                                    <div className="bg-green-50 p-4 rounded-md mb-6 border border-green-200">
                                        <h3 className="text-xl font-semibold text-green-800 mb-3">
                                            Brazalete Trampolines
                                        </h3>
                                        <p className="text-green-700 mb-3">
                                            Precio por brazalete: $
                                            {adjustedTrampolineBraceletProduct.price.toFixed(2)}
                                        </p>

                                        <div className="mb-4">
                                            <button
                                                type="button"
                                                onClick={() => setSelectedBraceletId(adjustedTrampolineBraceletProduct.id)}
                                                className={`p-3 border rounded-md text-sm font-medium text-center w-full transition duration-200
                                                    ${selectedBraceletId === adjustedTrampolineBraceletProduct.id
                                                        ? "bg-green-600 text-white shadow-md"
                                                        : "bg-white text-green-700 hover:bg-green-100 border-green-300"
                                                    }`}
                                            >
                                                Brazalete Trampolines
                                            </button>
                                        </div>

                                        {selectedBraceletId && (
                                            <>
                                                {/* Selector de Cantidad */}
                                                <div className="flex items-center gap-2 mt-3 mb-4">
                                                    <label htmlFor="hourlyQty" className="text-gray-700">
                                                        Cantidad:
                                                    </label>
                                                    <input
                                                        type="number"
                                                        id="hourlyQty"
                                                        min="1"
                                                        value={braceletQuantity}
                                                        onChange={(e) =>
                                                            setBraceletQuantity(parseInt(e.target.value) || 1)
                                                        }
                                                        className="w-20 p-2 border rounded-md text-center"
                                                    />
                                                </div>

                                                {/* Selector de Hora */}
                                                <div className="mb-3">
                                                    <label htmlFor="timeSlot" className="block text-gray-700 mb-1">
                                                        Selecciona tu Franja Horaria (11:00 AM - 9:00 PM):
                                                    </label>
                                                    <select
                                                        id="timeSlot"
                                                        value={selectedTime}
                                                        onChange={(e) => setSelectedTime(e.target.value)}
                                                        className="w-full p-2 border rounded-md bg-white"
                                                        required
                                                    >
                                                        <option value="">Selecciona una franja horaria</option>
                                                        {generateTimeOptions().map((time) => (
                                                            <option key={time} value={time}>
                                                                {time}
                                                            </option>
                                                        ))}
                                                    </select>
                                                    {!selectedTime && (
                                                        <p className="text-red-500 text-sm mt-1">
                                                            Por favor, selecciona una franja horaria para tu brazalete.
                                                        </p>
                                                    )}
                                                </div>
                                            </>
                                        )}
                                        {!adjustedTrampolineBraceletProduct && (
                                            <p className="text-red-500 text-sm mt-2">
                                                No hay brazaletes de trampolines disponibles para la fecha seleccionada.
                                            </p>
                                        )}
                                    </div>
                                )}
                                {/* Si no se encontró el Brazalete de Trampolines para la fecha seleccionada */}
                                {clientType === "adultOrOver6" &&
                                    !adjustedTrampolineBraceletProduct && (
                                        <p className="text-red-500 text-sm mt-2">
                                            No hay brazaletes de trampolines
                                            disponibles.
                                        </p>
                                    )}

                                {/* Sección de Medias Especiales */}
                                <div className="bg-yellow-50 p-4 rounded-md border border-yellow-200 mb-6">
                                    <h3 className="text-xl font-semibold text-yellow-800 mb-3">
                                        Medias Especiales
                                    </h3>
                                    <p className="text-yellow-700 mb-2">
                                        Obligatorias para usar los trampolines
                                        (compra única por brazalete).
                                    </p>

                                    <div className="mb-3">
                                        <label
                                            htmlFor="sockTalla"
                                            className="block text-gray-700 mb-1"
                                        >
                                            Selecciona Talla:
                                        </label>
                                        <select
                                            id="sockTalla"
                                            value={selectedSockTallaId}
                                            onChange={(e) =>
                                                setSelectedSockTallaId(
                                                    parseInt(e.target.value) ||
                                                    ""
                                                )
                                            }
                                            className="w-full p-2 border rounded-md bg-white"
                                            disabled={
                                                !selectedBraceletId ||
                                                socksProducts.length === 0
                                            }
                                        >
                                            <option value="">
                                                Selecciona la talla de medias
                                            </option>
                                            {socksProducts.map((sock) => (
                                                <option
                                                    key={sock.id}
                                                    value={sock.id}
                                                >
                                                    {sock.name} - $
                                                    {sock.price.toFixed(2)}
                                                </option>
                                            ))}
                                        </select>
                                        {selectedBraceletId &&
                                            !selectedSockTallaId &&
                                            socksProducts.length > 0 && (
                                                <p className="text-red-500 text-sm mt-1">
                                                    Por favor, selecciona una
                                                    talla de medias.
                                                </p>
                                            )}
                                        {socksProducts.length === 0 && (
                                            <p className="text-red-500 text-sm mt-1">
                                                No hay medias disponibles.
                                            </p>
                                        )}
                                    </div>
                                    {selectedSockTallaId && (
                                        <div className="flex items-center gap-2">
                                            <label
                                                htmlFor="sockQty"
                                                className="text-gray-700"
                                            >
                                                Cantidad:
                                            </label>
                                            <span className="font-semibold text-gray-800">
                                                {sockQuantity}
                                            </span>
                                        </div>
                                    )}
                                </div>

                                {/* Botón Añadir al Carrito */}
                                <div className="mt-6 text-center">
                                    <button
                                        type="button"
                                        onClick={handleAddToCart}
                                        disabled={
                                            !selectedBraceletId ||
                                            (clientType === "adultOrOver6" && !selectedTime) ||
                                            !selectedSockTallaId ||
                                            braceletQuantity < 1
                                        }
                                        className={`py-3 px-8 rounded-lg font-bold text-white transition duration-300
                                            ${
                                                !selectedBraceletId ||
                                                (clientType === "adultOrOver6" && !selectedTime) ||
                                                !selectedSockTallaId ||
                                                braceletQuantity < 1
                                                    ? "bg-gray-400 cursor-not-allowed"
                                                    : "bg-blue-600 hover:bg-blue-700 shadow-md"
                                            }
                                        `}
                                    >
                                        Añadir al Carrito
                                    </button>
                                    {mensaje && (
                                        <p
                                            className={`mt-4 text-sm font-semibold ${
                                                mensaje.includes("añadidos")
                                                    ? "text-green-600"
                                                    : "text-red-600"
                                            }`}
                                        >
                                            {mensaje}
                                        </p>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Columna del Carrito */}
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">
                            Tu Carrito
                        </h2>
                        {cartItems.length === 0 ? (
                            <p className="text-gray-600 text-center">
                                El carrito está vacío.
                            </p>
                        ) : (
                            <div className="space-y-4">
                                {cartItems.map((item) => (
                                    <div
                                        key={item.uniqueId}
                                        className="flex items-center justify-between border-b pb-3"
                                    >
                                        <div>
                                            <p className="font-semibold text-gray-800">
                                                {item.product.name}
                                                {item.selectedTime && ` (${item.selectedTime})`}
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                Fecha: {item.selectedDate}
                                                {item.clientType === "under6" && " (Niño < 6)"}
                                                {item.clientType === "adultOrOver6" && " (Adulto/ > 6)"}
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                ${item.product.price.toFixed(2)}{" "}
                                                x {item.quantity}
                                            </p>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <button
                                                onClick={() =>
                                                    handleUpdateCartItemQuantity(
                                                        item.uniqueId,
                                                        item.quantity - 1
                                                    )
                                                }
                                                className="bg-red-500 hover:bg-red-600 text-white p-1 rounded-full w-6 h-6 flex items-center justify-center text-lg"
                                            >
                                                -
                                            </button>
                                            <span className="font-semibold">
                                                {item.quantity}
                                            </span>
                                            <button
                                                onClick={() =>
                                                    handleUpdateCartItemQuantity(
                                                        item.uniqueId,
                                                        item.quantity + 1
                                                    )
                                                }
                                                className="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full w-6 h-6 flex items-center justify-center text-lg"
                                            >
                                                +
                                            </button>
                                            <button
                                                onClick={() =>
                                                    handleRemoveFromCart(
                                                        item.uniqueId
                                                    )
                                                }
                                                className="ml-3 text-red-500 hover:text-red-700"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        <div className="mt-8 pt-4 border-t-2 border-gray-200">
                            <div className="flex justify-between items-center text-xl font-bold text-gray-900">
                                <span>Total:</span>
                                <span>${totalCartPrice.toFixed(2)}</span>
                            </div>
                            <div className="mt-6 text-center">
                                <button
                                    onClick={handleSubmitCheckout}
                                    disabled={cartItems.length === 0}
                                    className={`py-3 px-8 rounded-lg font-bold text-white transition duration-300 w-full
                                        ${
                                            cartItems.length === 0
                                                ? "bg-gray-400 cursor-not-allowed"
                                                    : "bg-purple-600 hover:bg-purple-700 shadow-md"
                                                }
                                    `}
                                >
                                    Proceder al Pago
                                </button>
                            </div>
                            {mensaje && (
                                <p
                                    className={`mt-4 text-sm font-semibold ${
                                        mensaje.includes("vacío")
                                            ? "text-red-600"
                                            : "text-green-600"
                                    }`}
                                >
                                    {mensaje}
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Tienda;