<?php
/**
 * Copyright (C) 2015-2020 Virgil Security Inc.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3) Neither the name of the copyright holder nor the names of its
 *     contributors may be used to endorse or promote products derived from
 *     this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ''AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Lead Maintainer: Virgil Security Inc. <support@virgilsecurity.com>
 */

namespace Core;

use Virgil\Crypto\Exceptions\VirgilCryptoException;

/**
 * Class TokenHelper
 * @package Core
 */
class TokenHelper
{
    /**
     * @return string
     * @throws \Exception
     */
    public function generateToken()
    {
        if ((int)phpversion() < 7) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 32; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        } else {
            $randomString = base64_encode(random_bytes(32));
        }

        return $randomString;
    }

    /**
     * @param $token
     * @param $identity
     */
    public function setTokenValue($token, $identity)
    {
        session_start();
        if (!isset($_SESSION['virgil'][$token])) {
            $_SESSION['virgil'][$token] = $identity;
        }
    }

    /**
     * @param $token
     * @return bool
     */
    public function isTokenExists($token)
    {
        session_start();
        return array_key_exists($token, $_SESSION['virgil']);
    }

    /**
     * @param $token
     * @return mixed
     */
    public function getIdentityValue($token)
    {
        return $_SESSION['virgil'][$token];
    }

    /**
     * @param $token
     * @return string
     * @throws VirgilCryptoException
     */
    public function getJWT($token)
    {
        $JWTGenerator = new JWTGenerator();

        return $JWTGenerator->generate($this->getIdentityValue($token));
    }

}
