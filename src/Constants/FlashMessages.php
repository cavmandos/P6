<?php

namespace App\Constants;

class FlashMessages
{
    const RECOVERY_OK = 'Email de récupération envoyé';
    const RECOVERY_KO = 'Aucun utilisateur trouvé avec cet email';
    const PASSWORD_OK = 'Votre mot de passe a bien été changé';
    const ACTIVATION_OK = 'Votre compte est maintenant activé ! GO RIDE !';
    const ALREADY_NAME = 'Un utilisateur avec le même nom existe déjà.';
    const ALREADY_EMAIL = 'Un utilisateur avec le même email existe déjà.';
    const SIGNUP_OK = 'Félicitation Rider ! Vous allez recevoir un email pour valider votre inscription.';
    const TRICK_OK = 'Félicitations, vous avez créé le trick : ';
    const PROBLEM = 'Oups, il semble y avoir un souci';
    const MESSAGE_OK = 'Votre message a bien été publié';
    const TRICK_UPDATE = 'Félicitations, vous avez modifié le trick : ';
    const BANNER_CHANGE = "Héhé, ça c'est un bon choix de bannière :-)";
    const TRICK_DELETE = "So long ! Vous avez supprimé le trick : ";
    const MEDIA_DELETE = "Le media a bien été supprimé";
    const MESSAGE_DELETE = "Ce commentaire a bien été supprimé";
    const TOO_LARGE = 'Le fichier fourni est trop volumineux (maxi 1mo)';
}