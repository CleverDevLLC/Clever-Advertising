pragma solidity ^0.4.12;
import './iftokenholder.sol';
import './iferc20token.sol';

contract IFEtherToken is IFTokenHolder, IFERC20Token {
    function deposit() public payable;
    function withdraw(uint256 _amount) public;
    function withdrawTo(address _to, uint256 _amount);
}