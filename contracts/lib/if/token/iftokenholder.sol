pragma solidity ^0.4.12;
import './ifowned.sol';
import './iferc20token.sol';

contract IFTokenHolder is IFOwned {
    function withdrawTokens(IFERC20Token _token, address _to, uint256 _amount) public;
}