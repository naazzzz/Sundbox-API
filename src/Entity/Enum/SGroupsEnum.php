<?php

namespace App\Entity\Enum;

enum SGroupsEnum: string
{
    case GET_LEGAL_ENTITY = 'GetLegalEntity';
    case SET_LEGAL_ENTITY = 'SetLegalEntity';
    case GET_USER = 'GetUser';
    case SET_USER = 'SetUser';
    case GET_BASE = 'GetBase';
    case GET_BASE_OBJ = 'GetObjBase';
    case GET_SPARE_PARTS = 'GetSpareParts';
    case SET_SPARE_PARTS = 'SetSpareParts';
    case GET_JOB = 'GetJob';
    case SET_JOB = 'SetJob';
}
