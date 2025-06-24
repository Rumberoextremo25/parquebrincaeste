// resources/js/Components/Notification/ErrorToast.jsx

import { XCircleIcon, XMarkIcon } from '@heroicons/react/24/solid'
import React from 'react'
import { toast } from 'react-hot-toast' // Aunque 'toast' se pasa como prop, esta importación podría ser necesaria si lo usas directamente en otro lugar.
import ContainerToast from './ContainerToast'

const ErrorToast = ({ toast, onDismiss, errors = {} }) => { // Cambiado 'errors = []' a 'errors = {}' porque suele ser un objeto

    // Aseguramos que 'errors' sea un objeto para Object.keys()
    if (typeof errors !== 'object' || errors === null) {
        errors = {};
    }

    // Aplanamos los errores para listarlos de forma simple,
    // manejando tanto strings como arrays de mensajes por campo.
    const allErrorMessages = [];
    Object.keys(errors).forEach(key => {
        const errorValue = errors[key];
        if (Array.isArray(errorValue)) {
            errorValue.forEach((message, index) => {
                allErrorMessages.push({ id: `${key}-${index}`, message: message });
            });
        } else {
            allErrorMessages.push({ id: key, message: errorValue }); // Si es un string simple, la clave del campo es suficiente
        }
    });

    return (
        <ContainerToast toast={toast}>
            <div className='p-4 bg-red-50 w-full'>
                <div className='flex'>
                    <div className='shrink-0'>
                        <XCircleIcon className='text-red-400 h-5 w-5' />
                    </div>
                    <div className='ml-3 grow'>
                        <h3 className='text-red-800 font-medium text-sm'>
                            Hubo {allErrorMessages.length} errores con su envío
                        </h3>
                        <div className='text-sm mt-2'>
                            <ul role="list" className='text-red-700 pl-5 list-disc space-y-1'>
                                {/* ¡Aquí está la corrección! */}
                                {allErrorMessages.map((errorItem) => (
                                    <li key={errorItem.id}>{errorItem.message}</li>
                                ))}
                            </ul>
                        </div>
                    </div>
                    <div className='text-red-800 text-sm justify-self-end font-medium'>
                        <button onClick={onDismiss} className="">
                            <XMarkIcon className='text-red-500 h-4 w-4' />
                        </button>
                    </div>
                </div>
            </div>
        </ContainerToast>
    )
}

export default ErrorToast