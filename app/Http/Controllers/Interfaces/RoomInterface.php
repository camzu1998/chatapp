<?php

namespace App\Http\Controllers\Interfaces;

use Illuminate\Http\Request;

interface RoomInterface 
{
    public function get_user_rooms(string $switch_response): mixed;

    public function save_room(Request $request): mixed;

    public function update_room_status(Request $request, int $room_id): mixed;
    /**
     * Upload room profile image
     */
    public function upload_room_profile(int $room_id, Request $request): string;
    /**
     *  Update room data
     */
    public function update(Request $request, int $room_id): mixed;
    /**
     *  Get room roommates
     */
    public function get_roommates(int $room_id): array;
    /**
     * Delete room and connected data
     */
    public function delete_room(int $room_id): mixed;
    /**
     * Send invites to friends
     */
    public function invite(int $room_id, Request $request): mixed;
    /**
     *  Get room data
     */
    public function get_room(int $room_id): mixed;
    /**
     * Return room view
     */
    public function load_room(int $room_id, Request $request): mixed;
}