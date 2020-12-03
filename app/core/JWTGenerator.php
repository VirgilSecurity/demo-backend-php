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
use Virgil\Crypto\VirgilCrypto;
use Virgil\Sdk\Web\Authorization\JwtGenerator as SDKJWTGenerator;

/**
 * Class AccessTokenGenerator
 * @package Core
 */
class JWTGenerator
{
    /**
     * @param string $identity
     * @return string
     * @throws VirgilCryptoException
     */
    public function generate(string $identity)
    {
        // API_KEY (You got this Key at Virgil Dashboard)
        $privateKeyStr = $_ENV['APP_KEY'];
        $apiKeyData = base64_decode($privateKeyStr);

        // Crypto library imports a private key into a necessary format
        $crypto = new VirgilCrypto();
        $privateKey = $crypto->importPrivateKey($apiKeyData);

        // Use your App Credentials you got at Virgil Dashboard:
        $appId = $_ENV['APP_ID']; // APP_ID
        $apiKeyId = $_ENV['APP_KEY_ID']; // API_KEY_ID
        $ttl = 3600; // JWT's lifetime

        // Setup JWT generator with necessary parameters:
        $jwtGenerator = new SDKJwtGenerator($privateKey->getPrivateKey(), $apiKeyId, $crypto, $appId, $ttl);

        // Generate JWT for a user
        // Remember that you must provide each user with his unique JWT
        // Each JWT contains unique user's identity (in this case - Alice)
        // Identity can be any value: name, email, some id etc.
        $token = $jwtGenerator->generateToken($identity);

        // As result you get users JWT, it looks like this: "{string}.{string}.{string}"
        // You can provide users with JWT at registration or authorization steps
        // Send a JWT to client-side
        return $token->__toString();
    }
}
