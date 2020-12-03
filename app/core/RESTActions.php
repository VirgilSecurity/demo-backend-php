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

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

/**
 * Class RESTActions
 * @package Core
 */
class RESTActions
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var TokenHelper
     */
    private $tokenHelper;

    /**
     * RESTActions constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Initialize REST endpoints
     */
    public function init()
    {
        $this->postAuthenticate();
        $this->getVirgilJWT();
    }

    /**
     * Generate random token-string for identity and store it to the session (in this case, but you can use DB, etc.)
     * Return {"authToken": "{string}"} or {"error-massage": "{string}"}
     */
    public function postAuthenticate()
    {
        $this->app->post('/authenticate', function (Request $request, Response $response) {

            $body = $request->getParsedBody();

            if (!isset($body['identity']) || $body['identity'] == '') {

                $res = ResponseResult::format(['error-message' => 'No identity key in the request`s body (or empty value)'], 400);

            } else {

                $token = $this->tokenHelper->generateToken();
                $this->tokenHelper->setTokenValue($token, $body['identity']);

                $res = ResponseResult::format(['authToken' => $token], 200);
            }

            return $response->withJson($res['data'], $res['status']);
        });
    }

    /**
     * Check header Authorization: 'Bearer ${token}'
     * Return JWT {"virgilToken": "{string}"} or {"error-massage": "{string}"}
     */
    public function getVirgilJWT()
    {
        $this->app->get('/virgil-jwt', function (Request $request, Response $response) {

            $header = $request->getHeader('Authorization');
            $token = explode("Bearer ", $header[0]);

            if(is_null($token[1])) {
                $res = ResponseResult::format(['error-message' => 'Unauthorized'], 401);
            }
            else {
                if($this->tokenHelper->isTokenExists($token[1])) {
                    $jwt = $this->tokenHelper->getJWT($token[1]);

                    $res = ResponseResult::format(['virgilToken' => $jwt], 200);
                } else {
                    $res = ResponseResult::format(['error-message' => 'Invalid token'], 400);
                }
            }

            return $response->withJson($res['data'], $res['status']);
        });
    }
}
