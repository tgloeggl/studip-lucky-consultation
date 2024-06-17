<?php

return [
    "studip-current-user" => function () {
        $user = $GLOBALS["user"];
        if ($user) {
            return $user->getAuthenticatedUser();
        }

        return null;
    }
];
