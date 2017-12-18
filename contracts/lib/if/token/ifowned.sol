pragma solidity ^0.4.12;

contract IFOwned {
    function owner() public constant returns (address) {}

    function transferOwnership(address _newOwner) public;
    function acceptOwnership() public;
}